<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parcel\StoreParcelRequest;
use App\Http\Resources\ParcelCollection;
use App\Http\Resources\ParcelResource;
use App\Models\Parcel;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/parcels",
     *     summary="Get list of parcels",
     *     tags={"Parcels"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="tracking_code",
     *         in="query",
     *         description="Filter by tracking code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sender_mobile",
     *         in="query",
     *         description="Filter by sender mobile",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="receiver_mobile",
     *         in="query",
     *         description="Filter by receiver mobile",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="tracking_code", type="string", example="TRK170420234567"),
     *                     @OA\Property(property="weight", type="number", format="float", example=2.5),
     *                     @OA\Property(property="dimensions", type="object",
     *                         @OA\Property(property="length", type="number", format="float", example=30),
     *                         @OA\Property(property="width", type="number", format="float", example=20),
     *                         @OA\Property(property="height", type="number", format="float", example=15)
     *                     ),
     *                     @OA\Property(property="sender", type="object",
     *                         @OA\Property(property="name", type="string", example="علی رضایی"),
     *                         @OA\Property(property="mobile", type="string", example="09123456789"),
     *                         @OA\Property(property="postal_code", type="string", example="1234567890"),
     *                         @OA\Property(property="address", type="string", example="تهران، خیابان ولیعصر")
     *                     ),
     *                     @OA\Property(property="receiver", type="object",
     *                         @OA\Property(property="name", type="string", example="محمد حسینی"),
     *                         @OA\Property(property="mobile", type="string", example="09351234567"),
     *                         @OA\Property(property="postal_code", type="string", example="0987654321"),
     *                         @OA\Property(property="address", type="string", example="مشهد، بلوار وکیل آباد")
     *                     ),
     *                     @OA\Property(property="created_at", type="string", example="2024-01-01 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-01-01 12:00:00")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15)
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost:8000/api/parcels?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost:8000/api/parcels?page=5"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://localhost:8000/api/parcels?page=2")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): ParcelCollection
    {
        $query = Parcel::with(['sender', 'receiver'])
            ->latest();

        // فیلتر بر اساس کد رهگیری
        if ($request->has('tracking_code') && $request->tracking_code) {
            $query->where('tracking_code', 'like', '%' . $request->tracking_code . '%');
        }

        // فیلتر بر اساس موبایل فرستنده
        if ($request->has('sender_mobile') && $request->sender_mobile) {
            $query->whereHas('sender', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->sender_mobile . '%');
            });
        }

        // فیلتر بر اساس موبایل گیرنده
        if ($request->has('receiver_mobile') && $request->receiver_mobile) {
            $query->whereHas('receiver', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->receiver_mobile . '%');
            });
        }

        $perPage = $request->get('per_page', 15);
        $parcels = $query->paginate($perPage);

        return new ParcelCollection($parcels);
    }

    /**
     * @OA\Post(
     *     path="/parcels",
     *     summary="Create a new parcel",
     *     tags={"Parcels"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Parcel data with sender, receiver, weight, dimensions and tracking code",
     *         @OA\JsonContent(
     *             required={"sender","receiver","weight","dimensions","tracking_code"},
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
     *             ),
     *             @OA\Property(
     *                 property="tracking_code",
     *                 type="string",
     *                 example="608850427600032253168114",
     *                 description="کد رهگیری 24 رقمی پستی ایران (الزامی)"
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
     *                 @OA\Property(property="tracking_code", type="string", example="608850427600032253168114"),
     *                 @OA\Property(property="formatted_tracking_code", type="string", example="6088-5041-8600-0322-5006-8114"),
     *                 @OA\Property(property="weight", type="number", format="float", example=2.5),
     *                 @OA\Property(
     *                     property="dimensions",
     *                     type="object",
     *                     @OA\Property(property="length", type="number", format="float", example=30),
     *                     @OA\Property(property="width", type="number", format="float", example=20),
     *                     @OA\Property(property="height", type="number", format="float", example=15)
     *                 ),
     *                 @OA\Property(
     *                     property="sender",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="علی رضایی"),
     *                     @OA\Property(property="mobile", type="string", example="09123456789"),
     *                     @OA\Property(property="postal_code", type="string", example="1234567890"),
     *                     @OA\Property(property="address", type="string", example="تهران، خیابان ولیعصر")
     *                 ),
     *                 @OA\Property(
     *                     property="receiver",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="محمد حسینی"),
     *                     @OA\Property(property="mobile", type="string", example="09351234567"),
     *                     @OA\Property(property="postal_code", type="string", example="0987654321"),
     *                     @OA\Property(property="address", type="string", example="مشهد، بلوار وکیل آباد")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01 12:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-01 12:00:00")
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
     *                     property="tracking_code",
     *                     type="array",
     *                     @OA\Items(type="string", example="کد رهگیری الزامی است")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreParcelRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $parcel = DB::transaction(function () use ($validated) {
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

            // ایجاد مرسوله - tracking_code اجباری است و از کاربر دریافت می‌شود
            return Parcel::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'weight' => $validated['weight'],
                'length' => $validated['dimensions']['length'],
                'width' => $validated['dimensions']['width'],
                'height' => $validated['dimensions']['height'],
                'tracking_code' => $validated['tracking_code'],
            ]);
        });

        $parcel->load(['sender', 'receiver']);

        return response()->json([
            'message' => 'Parcel created successfully',
            'data' => new ParcelResource($parcel)
        ], 201);
    }




}


