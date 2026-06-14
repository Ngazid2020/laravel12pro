<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * GET /api/v1/directory
     * Annuaire des membres actifs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::whereHas('profile', fn ($q) => $q->where('membership_status', 'active'))
            ->with('profile');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhereHas('profile', fn ($p) => $p
                    ->where('company_name', 'like', "%{$term}%")
                    ->orWhere('sector', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                )
            );
        }

        if ($request->filled('sector')) {
            $query->whereHas('profile', fn ($p) => $p->where('sector', $request->sector));
        }

        $members = $query->paginate(20);

        return response()->json(UserResource::collection($members)->response()->getData(true));
    }

    /**
     * GET /api/v1/directory/{user}
     * Profil public d'un membre.
     */
    public function show(User $user): JsonResponse
    {
        $profile = $user->profile;

        abort_unless($profile && $profile->membership_status === 'active', 404);

        $user->load('profile');

        return response()->json(new UserResource($user));
    }
}