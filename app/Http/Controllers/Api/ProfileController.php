<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * GET /api/v1/profile
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json(
            new UserResource($request->user()->load('profile.mentor'))
        );
    }

    /**
     * PUT /api/v1/profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name'     => ['sometimes', 'string', 'max:100'],
            'last_name'      => ['sometimes', 'string', 'max:100'],
            'phone'          => ['sometimes', 'string', 'max:30'],
            'company_name'   => ['sometimes', 'nullable', 'string', 'max:200'],
            'project_name'   => ['sometimes', 'nullable', 'string', 'max:200'],
            'sector'         => ['sometimes', 'nullable', 'string', 'max:100'],
            'city'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'bio'            => ['sometimes', 'nullable', 'string', 'max:2000'],
            'skills_offered' => ['sometimes', 'nullable', 'array'],
            'needs_expressed'=> ['sometimes', 'nullable', 'array'],
            'social_links'   => ['sometimes', 'nullable', 'array'],
        ]);

        // Mettre à jour les champs User
        $userFields = array_intersect_key($validated, array_flip(['first_name', 'last_name', 'phone']));
        if (! empty($userFields)) {
            $user->update($userFields);
        }

        // Mettre à jour le profil
        $profileFields = array_diff_key($validated, array_flip(['first_name', 'last_name', 'phone']));
        if (! empty($profileFields) && $user->profile) {
            $user->profile->update($profileFields);
        }

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user'    => new UserResource($user->fresh()->load('profile')),
        ]);
    }

    /**
     * POST /api/v1/profile/avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Supprimer l'ancien avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'message'    => 'Avatar mis à jour.',
            'avatar_url' => asset('storage/'.$path),
        ]);
    }
}