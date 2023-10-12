<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function checkEmailExists(Request $request): JsonResponse
    {
        try {
            $count = User::where('email', $request->email)->count();

            return response()->json([
                'success' => true,
                'isEmailAvailable' => $count > 0 ? false : true,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkCredentials(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string']
            ]);

            $user = User::where('email', $data['email'])->first();

            if ($user) {
                if (Hash::check($data['password'], $user->password)) {
                    return response()->json([
                        'success' => true,
                        'error' => '',
                    ], JsonResponse::HTTP_OK);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Incorrect password.',
                    ], JsonResponse::HTTP_ACCEPTED);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Email not found.',
                ], JsonResponse::HTTP_ACCEPTED);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
