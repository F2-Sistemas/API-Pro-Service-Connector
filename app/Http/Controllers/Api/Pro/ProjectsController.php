<?php

namespace App\Http\Controllers\Api\Pro;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProfessionalProject;
use App\Models\ProjectCategory;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();
        $professional = $request?->user()?->professional;

        if (!$professional) {
            $query = $query?->whereId(0);
        }

        $perPage = $request->input('per_page') ?: $request->input('perPage');
        $perPage = filter_var($perPage, FILTER_VALIDATE_INT) && $perPage > 0 ? intval($perPage) : 20;
        $urgentOnly = $request->boolean('urgentOnly');
        $categories = $request->input('categories');
        $categories = $categories && is_string($categories) ?
            array_filter(explode(',', $categories), fn ($item) => filter_var($item, FILTER_VALIDATE_INT))
            : null;

        $query = $query->activeOnly()
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

        abort_unless($project, 404);

        return response()->json($project);
    }

    public function projectRelease(Request $request, int|string $projectId, int|string|null $coinPrice = null)
    {
        $professional = $request?->user()?->professional;

        if (!$professional) {
            return response()->json([
                'error' => __('Not found!'),
            ], 404);
        }

        $coinPrice ??= $request->integer('coinPrice');

        if (!$coinPrice) {
            return response()->json([
                'error' => __('Coin price confirmation is required')
            ], 422);
        }

        $project = Project::query()
            ->activeOnly()
            ->where('id', $projectId)
            ->with([
                'professionalProject' => fn (\Illuminate\Database\Eloquent\Relations\HasMany $q) => $q->where('professional_id', $professional?->id)
            ])
            ->first();

        if (!$project) {
            return response()->json([
                'error' => __('Not found record!'),
            ], 404);
        }

        if ($project?->professionalProject?->first()) {
            return response()->json([
                'message' => __('You have already released this project before.'),
                'params' => [
                    'professional_id' => $professional?->id,
                    'project_id' => $project?->id,
                    'coinPrice' => $coinPrice,
                ],
            ], 422);
        }

        if (!$project->enterCoinIsValid($coinPrice)) {
            return response()->json([
                'message' => __('Invalid coin price confirmation.'),
                'params' => [
                    'professional_id' => $professional?->id,
                    'project_id' => $project?->id,
                    'coinPrice' => $coinPrice,
                ],
            ], 422);
        }

        // Aqui verificar se o profissional tem saldo para liberar o projeto

        $professionalProject = ProfessionalProject::firstOrCreate([
            'professional_id' => $professional?->id,
            'project_id' => $project?->id,
        ]);

        return response()->json(
            array_merge(
                [
                    'project' => $project,
                ],
                $professionalProject?->toArray() ?? [],
            )
        );
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

        abort_unless($professionalProject, 404);

        return response()->json($professionalProject);
    }

    public function categoryIndex(Request $request)
    {
        return response()->json(
            cache()->remember(
                md5(__METHOD__),
                60 * 30,
                fn () => ProjectCategory::orderBy('title')
                    ->paginate(50),
            )
        );
    }
}
