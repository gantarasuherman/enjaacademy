<?php

declare(strict_types=1);

namespace App\Services\System;

use App\Services\Audit\AuditLogger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * mysqldump-based backups written to the configured disk.
 */
class BackupService
{
    public function __construct(private readonly AuditLogger $audit) {}

    private function disk()
    {
        return Storage::disk(config('admin.backup.disk', 'local'));
    }

    private function directory(): string
    {
        return trim((string) config('admin.backup.path', 'backups'), '/');
    }

    /** @return Collection<int, array{name:string, size:int, created_at:Carbon}> */
    public function list(): Collection
    {
        $disk = $this->disk();
        $dir = $this->directory();

        if (! $disk->exists($dir)) {
            return collect();
        }

        return collect($disk->files($dir))
            ->filter(fn (string $path) => str_ends_with($path, '.sql') || str_ends_with($path, '.sql.gz'))
            ->map(fn (string $path) => [
                'name' => basename($path),
                'path' => $path,
                'size' => $disk->size($path),
                'created_at' => Carbon::createFromTimestamp($disk->lastModified($path)),
            ])
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * @return string the created file name
     *
     * @throws ProcessFailedException when mysqldump is unavailable or fails
     */
    public function create(): string
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        $filename = sprintf('backup-%s.sql', now()->format('Y-m-d_His'));
        $relative = $this->directory().'/'.$filename;

        $disk = $this->disk();
        $disk->makeDirectory($this->directory());

        $target = $disk->path($relative);

        $process = new Process([
            config('admin.backup.mysqldump_binary', 'mysqldump'),
            '--host='.$config['host'],
            '--port='.$config['port'],
            '--user='.$config['username'],
            '--password='.$config['password'],
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--default-character-set=utf8mb4',
            '--result-file='.$target,
            $config['database'],
        ]);

        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->prune();

        $this->audit->event('backup_created', __('Database backup :file created.', ['file' => $filename]));

        return $filename;
    }

    public function download(string $filename): string
    {
        $this->assertSafeName($filename);

        $path = $this->directory().'/'.$filename;

        abort_unless($this->disk()->exists($path), 404);

        return $this->disk()->path($path);
    }

    public function delete(string $filename): void
    {
        $this->assertSafeName($filename);

        $this->disk()->delete($this->directory().'/'.$filename);

        $this->audit->event('backup_deleted', __('Database backup :file deleted.', ['file' => $filename]));
    }

    /** Keep only the newest N archives. */
    private function prune(): void
    {
        $keep = (int) config('admin.backup.keep', 10);

        $this->list()
            ->slice($keep)
            ->each(fn (array $backup) => $this->disk()->delete($backup['path']));
    }

    /** Backup names come from the URL, so path traversal has to be blocked. */
    private function assertSafeName(string $filename): void
    {
        abort_if(
            $filename !== basename($filename) || ! preg_match('/^backup-[\w\-.]+\.sql(\.gz)?$/', $filename),
            400,
            'Invalid backup file name.',
        );
    }
}
