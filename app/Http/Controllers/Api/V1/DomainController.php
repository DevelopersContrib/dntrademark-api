<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\StoreDomainRequest;

use App\Http\Resources\DomainItemResource;
use App\Http\Resources\DomainResource;

use App\Models\Domain;
use App\Models\DomainItem;

class DomainController extends Controller
{
    private function isValidDomain($domain)
    {
        return preg_match('/^(?!-)[A-Za-z0-9-]{1,63}(?<!-)(\.[A-Za-z]{2,})+$/', $domain) ? true : false;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $domains = Domain::where('user_id', $user->id)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'domains' => DomainResource::collection($domains)
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreDomainRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            $domains = explode(',', $data['domains']);
            $validatedDomains = array();

            foreach ($domains as $d) {
                $domain = trim($d);
                if ($this->isValidDomain($domain)) {
                    $count = Domain::where('domain_name', $domain)->count();
                    if ($count < 1) {
                        array_push($validatedDomains, [
                            'user_id' => $user->id,
                            'domain_name' => $domain,
                            'no_of_items' => 0
                        ]);
                    }
                }
            }

            if (count($validatedDomains) > 0) {
                $isSaved = Domain::insert($validatedDomains);

                if ($isSaved) {
                    return response()->json([
                        'success' => $isSaved,
                        'message' => 'Domains successfuly saved.'
                    ], JsonResponse::HTTP_OK);
                } else {
                    return response()->json([
                        'success' => true,
                        'error' => 'Unable to save domain/s. Please try again!'
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'error' => count($domains) ? 'The domains already exist.' : 'The domain is already exists.'
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function count(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            return response()->json([
                'success' => true,
                'count' => Domain::where('user_id', $user->id)->count()
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function countHitDomains(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            return response()->json([
                'success' => true,
                'count' => Domain::where('user_id', $user->id)->where('no_of_items', '>', 0)->count()
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function countWithoutHitDomains(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            return response()->json([
                'success' => true,
                'count' => Domain::where('user_id', $user->id)->where('no_of_items', 0)->count()
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function countDomainRisks(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $domains = Domain::join()
                ->where('user_id', $user->id)
                ->where('status_label', 'like', '$pending$')
                ->where('registration_number', 'like', '%0000000%')
                ->where('status_definition', 'like', '%new%')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'domains' => DomainItemResource::collection($domains)
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
