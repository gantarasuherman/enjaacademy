<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentRepositoryInterface $enrollments) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Enrollment::class);

        return view('admin.enrollments.index', [
            'enrollments' => $this->enrollments->paginate(
                $request->only(['search', 'module', 'sort', 'direction']),
                $this->perPage(),
            ),
        ]);
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $this->authorize('delete', $enrollment);

        $this->enrollments->delete($enrollment);

        return back()->with('success', __('Enrollment was cancelled.'));
    }
}
