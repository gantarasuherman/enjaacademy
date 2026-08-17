<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Http\Resources\ModuleResource;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Unauthenticated — feeds the landing page's course catalog so a visitor can
 * browse before registering. Only metadata/syllabus, never lesson content:
 * `module()` deliberately doesn't eager-load lesson `items`, which is what
 * keeps `LessonResource` from exposing `content`/`translated_content` here.
 */
class PublicCatalogController extends Controller
{
    public function __construct(
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly LessonRepositoryInterface $lessons,
    ) {}

    public function modules(Request $request): AnonymousResourceCollection
    {
        return ModuleResource::collection(
            $this->modules->publicCatalog($request->only(['language', 'content_type', 'level'])),
        );
    }

    public function module(Request $request, string $moduleSlug): JsonResponse
    {
        $module = $this->modules->findBySlug($moduleSlug);

        abort_if($module === null || ! $module->is_active, 404);

        return response()->json([
            'data' => [
                'module' => new ModuleResource($module),
                'lessons' => LessonResource::collection($this->lessons->publishedForModule($module->id)),
            ],
        ]);
    }
}
