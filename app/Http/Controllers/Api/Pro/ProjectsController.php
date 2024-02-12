<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProfessionalProject;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page') ?: $request->input('perPage');
        $perPage = filter_var($perPage, FILTER_VALIDATE_INT) && $perPage > 0 ? intval($perPage) : 20;
        $urgentOnly = $request->boolean('urgentOnly');
        $categories = $request->input('categories');
        $categories = $categories && is_string($categories) ?
            array_filter(explode(',', $categories), fn ($item) => filter_var($item, FILTER_VALIDATE_INT))
            : null;

        $query = Project::query()
            ->activeOnly()
            ->when($urgentOnly, fn (Builder $q) => $q->where('urgent', true))
            ->when($categories, fn (Builder $q, $categoryIds) => $q->whereIn('project_category_id', $categoryIds));

        return response()->json(
            collect($query->paginate($perPage))->merge([
                'filters' => array_filter([
                    'perPage' => $perPage,
                    'urgentOnly' => $urgentOnly,
                    'categories' => $categories,
                ], fn ($item) => !is_null($item)),
            ])
        );
    }

    public function showOpenProject(Request $request, int|string $projectId)
    {
        $project = Project::query()
            ->activeOnly()
            ->where('id', $projectId)
            ->first();

        if (!$project) {
            return response()->json([
                'error' => __('Not found!'),
            ], 404);
        }

        return response()->json($project);
    }

    public function showProfessionalProject(Request $request, int|string $projectId)
    {
        $professional = $request?->user()?->professional;

        if (!$professional) {
            return response()->json([
                'error' => __('Not found!'),
            ], 404);
        }

        $professionalProject = ProfessionalProject::query()
            ->where('professional_id', $professional?->id)
            ->where('project_id', $projectId)
            ->with('project')
            ->whereHas('project')
            ->first();

        if (!$professionalProject) {
            return response()->json([
                'error' => __('Not found!'),
            ], 404);
        }

        return response()->json($professionalProject);
    }

    public function releaseProject(Request $request, null|int|string $projectId = null)
    {
        $projectId ??= $request->input('projectId');

        $request->merge([
            'projectId' => $projectId,
        ]);

        $request->validate([
            'projectId' => 'required|exists:App\Models\Project,id',
            // 'wallet_uuid' => 'required|exists:App\Models\Wallet,uuid', // TODO
            // 'project_price' => 'required|exists:App\Models\Wallet,uuid', // TODO
        ]);

        $professional = $request?->user()?->professional;

        if (!$professional) {
            return response()->json([
                'error' => __('Not found!'),
            ], 404);
        }

        $project = Project::query()
            ->activeOnly()
            ->where('id', $projectId)
            ->first();

        if (!$project) {
            return response()->json([
                'error' => __('Not found!'),
            ], 404);
        }

        return response()->json([
            'project' => $project,
        ]);
    }
}
