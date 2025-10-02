<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
//use App\Http\Requests\PostalPackages\StorePostalPackageRequest;
use App\Http\Resources\PostalPackageResource;
use App\Models\PostalPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="PostalPackages",
 *     description="API Endpoints for PostalPackage Management"
 * )
 */
class PostalPackageController extends Controller
{

    /**
     * @OA\Get(
     *     path="/postal-packages",
     *     summary="List all postal-packages",
     *     tags={"Postal Packages"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PostalPackageResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        return PostalPackageResource::collection(PostalPackage::query()->orderBy('id', 'desc')->paginate(50));
    }

//    /**
//     * @OA\Post(
//     *     path="/postalPackages",
//     *     summary="Create a new postalPackage",
//     *     tags={"PostalPackages"},
//     *     security={{"bearerAuth":{}}},
//     *     @OA\RequestBody(
//     *         required=true,
//     *         @OA\JsonContent(ref="#/components/schemas/StorePostalPackageRequest")
//     *     ),
//     *     @OA\Response(
//     *         response=201,
//     *         description="PostalPackage created successfully",
//     *         @OA\JsonContent(ref="#/components/schemas/PostalPackageResource")
//     *     ),
//     *     @OA\Response(response=422, description="Validation error")
//     * )
//     */
//    public function store(StorePostalPackageRequest $request)
//    {
//        $validated = $request->validated();
//        return $validated;
//        $validated['password'] = bcrypt($validated['password']);
//        //---------------------
//        // Check if image was given and save on local file system
//        if (isset($validated['image'])) {
//            $imageName = $this->saveImage($validated['image'], $this->imagePath);
//            $validated['image'] = $imageName;
//        }
//        //----------------------------
//        $postalPackage = PostalPackage::query()->create($validated);
//
//        return new PostalPackageResource($postalPackage);
//    }

}
