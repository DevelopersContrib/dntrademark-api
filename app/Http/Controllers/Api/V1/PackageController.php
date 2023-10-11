<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Resources\PackageResource;
use App\Models\Package;

class PackageController extends Controller
{
    //
    public function index(): JsonResponse
    {
        try {
            $packages = Package::all();

            return response()->json([
                'success' => true,
                'data' => [
                    'packages' => PackageResource::collection($packages),
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Package $package): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'packages' => new PackageResource($package),
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
