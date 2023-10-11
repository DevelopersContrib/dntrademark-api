<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

use App\Models\User;

class UserController extends Controller
{
    //
    public function index()
    {
    }

    public function update(User $user, UpdateUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->is_onboarding = $data['is_onboarding'];

            $status = $user->save();

            return response()->json([
                'success' => $status,
                'data' => [
                    'user' => new UserResource($user)
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
