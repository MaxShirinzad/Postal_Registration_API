<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostalPackage\StorePostalPackageRequest;
use App\Http\Resources\PostalPackageResource;
use App\Models\Person;
use App\Models\PostalPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

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



    /**
     * @OA\Post(
     *     path="/api/parcels",
     *     summary="Create a new parcel",
     *     tags={"Parcels"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"sender","receiver","weight","dimensions"},
     *             @OA\Property(
     *                 property="sender",
     *                 type="object",
     *                 required={"name","mobile","postal_code","address"},
     *                 @OA\Property(property="name", type="string", example="علی رضایی"),
     *                 @OA\Property(property="mobile", type="string", example="09123456789"),
     *                 @OA\Property(property="postal_code", type="string", example="1234567890"),
     *                 @OA\Property(property="address", type="string", example="تهران، خیابان ولیعصر")
     *             ),
     *             @OA\Property(
     *                 property="receiver",
     *                 type="object",
     *                 required={"name","mobile","postal_code","address"},
     *                 @OA\Property(property="name", type="string", example="محمد حسینی"),
     *                 @OA\Property(property="mobile", type="string", example="09351234567"),
     *                 @OA\Property(property="postal_code", type="string", example="0987654321"),
     *                 @OA\Property(property="address", type="string", example="مشهد، بلوار وکیل آباد")
     *             ),
     *             @OA\Property(property="weight", type="number", format="float", example=2.5),
     *             @OA\Property(
     *                 property="dimensions",
     *                 type="object",
     *                 required={"length","width","height"},
     *                 @OA\Property(property="length", type="number", format="float", example=30),
     *                 @OA\Property(property="width", type="number", format="float", example=20),
     *                 @OA\Property(property="height", type="number", format="float", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Parcel created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Parcel created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="tracking_code", type="string", example="TRK170420234567"),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01 12:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sender.mobile",
     *                     type="array",
     *                     @OA\Items(type="string", example="فرمت شماره موبایل فرستنده صحیح نیست")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StorePostalPackageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // پیدا کردن یا ایجاد فرستنده
            $sender = Person::firstOrCreate(
                ['mobile' => $validated['sender']['mobile']],
                [
                    'name' => $validated['sender']['name'],
                    'postal_code' => $validated['sender']['postal_code'],
                    'address' => $validated['sender']['address'],
                ]
            );

            // پیدا کردن یا ایجاد گیرنده
            $receiver = Person::firstOrCreate(
                ['mobile' => $validated['receiver']['mobile']],
                [
                    'name' => $validated['receiver']['name'],
                    'postal_code' => $validated['receiver']['postal_code'],
                    'address' => $validated['receiver']['address'],
                ]
            );

            // ایجاد مرسوله
            $parcel = PostalPackage::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'weight' => $validated['weight'],
                'length' => $validated['dimensions']['length'],
                'width' => $validated['dimensions']['width'],
                'height' => $validated['dimensions']['height'],
            ]);

            return response()->json([
                'message' => 'Parcel created successfully',
                'data' => [
                    'id' => $parcel->id,
                    'tracking_code' => $parcel->tracking_code,
                    'created_at' => $parcel->created_at->toDateTimeString(),
                ]
            ], 201);
        });
    }
}
