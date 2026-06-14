<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    /**
     * GET /api/v1/opportunities
     */
    public function index(Request $request): JsonResponse
    {
        $query = Opportunity::where('is_active', true)
            ->with('partnerCompany')
            ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        $opportunities = $query->paginate(15);

        return response()->json(OpportunityResource::collection($opportunities)->response()->getData(true));
    }

    /**
     * GET /api/v1/opportunities/{opportunity}
     */
    public function show(Opportunity $opportunity): JsonResponse
    {
        abort_unless($opportunity->is_active, 404);

        $opportunity->load('partnerCompany');

        return response()->json(new OpportunityResource($opportunity));
    }

    /**
     * POST /api/v1/opportunities/{opportunity}/apply
     */
    public function apply(Request $request, Opportunity $opportunity): JsonResponse
    {
        abort_unless($opportunity->is_active, 404);

        $exists = OpportunityApplication::where('opportunity_id', $opportunity->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette opportunité.'], 422);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:50', 'max:2000'],
        ]);

        OpportunityApplication::create([
            'opportunity_id' => $opportunity->id,
            'user_id'        => $request->user()->id,
            'message'        => $validated['message'],
            'status'         => 'pending',
        ]);

        return response()->json(['message' => 'Candidature envoyée.'], 201);
    }

    /**
     * GET /api/v1/opportunities/my-applications
     */
    public function myApplications(Request $request): JsonResponse
    {
        $applications = OpportunityApplication::where('user_id', $request->user()->id)
            ->with('opportunity.partnerCompany')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($applications->through(fn ($app) => [
            'id'          => $app->id,
            'status'      => $app->status,
            'message'     => $app->message,
            'created_at'  => $app->created_at?->toISOString(),
            'opportunity' => new OpportunityResource($app->opportunity),
        ]));
    }
}