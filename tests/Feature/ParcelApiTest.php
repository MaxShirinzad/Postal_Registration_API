<?php

use App\Models\Parcel;
use App\Models\Person;

beforeEach(function () {
    $this->refreshDatabase();
});

// تست‌های ایجاد مرسوله
it('can create a parcel with valid data and tracking code', function () {
    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => '09123456789',
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => '608850418600032250068114',
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'tracking_code',
                'formatted_tracking_code',
                'weight',
                'dimensions' => [
                    'length',
                    'width',
                    'height',
                ],
                'sender' => [
                    'name',
                    'mobile',
                    'postal_code',
                    'address',
                ],
                'receiver' => [
                    'name',
                    'mobile',
                    'postal_code',
                    'address',
                ],
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJsonFragment([
            'tracking_code' => '608850418600032250068114',
            'weight' => 2.5,
        ]);

    // بررسی ذخیره شدن در دیتابیس
    $this->assertDatabaseHas('parcels', [
        'tracking_code' => '608850418600032250068114',
        'weight' => 2.5,
    ]);

    $this->assertDatabaseHas('people', [
        'mobile' => '09123456789',
        'name' => 'علی رضایی',
    ]);

    $this->assertDatabaseHas('people', [
        'mobile' => '09351234567',
        'name' => 'محمد حسینی',
    ]);
});

it('returns validation error when tracking code is missing', function () {
    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => '09123456789',
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        // tracking_code حذف شده
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['tracking_code'])
        ->assertJsonFragment([
            'message' => 'Validation errors',
        ]);
});

it('returns validation error for invalid tracking code format', function () {
    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => '09123456789',
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => 'invalid-tracking-code', // فرمت نامعتبر
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['tracking_code']);
});

it('returns validation error for duplicate tracking code', function () {
    // ایجاد یک مرسوله با کد رهگیری مشخص
    Parcel::factory()->create([
        'tracking_code' => '608850418600032250068114'
    ]);

    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => '09123456789',
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => '608850418600032250068114', // کد تکراری
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['tracking_code']);
});

it('returns validation error when sender and receiver have same mobile', function () {
    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => '09123456789',
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09123456789', // موبایل تکراری با فرستنده
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => '608850418600032250068114',
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['receiver.mobile']);
});

// تست‌های لیست مرسوله‌ها
it('can get list of parcels', function () {
    $sender = Person::factory()->create();
    $receiver = Person::factory()->create();

    Parcel::factory()->count(3)->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
    ]);

    $response = $this->getJson('/api/parcels');

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'tracking_code',
                    'formatted_tracking_code',
                    'weight',
                    'dimensions',
                    'sender',
                    'receiver',
                    'created_at',
                ]
            ],
            'meta',
            'links',
        ])
        ->assertJsonCount(3, 'data');
});

it('can filter parcels by tracking code', function () {
    $sender = Person::factory()->create();
    $receiver = Person::factory()->create();

    $parcel1 = Parcel::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'tracking_code' => '608850418600032250068114',
    ]);

    $parcel2 = Parcel::factory()->create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'tracking_code' => '608850418600032250068115',
    ]);

    $response = $this->getJson('/api/parcels?tracking_code=608850418600032250068114');

    $response
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['tracking_code' => '608850418600032250068114']);
});

it('can filter parcels by sender mobile', function () {
    $sender1 = Person::factory()->create(['mobile' => '09123456789']);
    $sender2 = Person::factory()->create(['mobile' => '09351234567']);
    $receiver = Person::factory()->create();

    Parcel::factory()->create(['sender_id' => $sender1->id, 'receiver_id' => $receiver->id]);
    Parcel::factory()->create(['sender_id' => $sender2->id, 'receiver_id' => $receiver->id]);

    $response = $this->getJson('/api/parcels?sender_mobile=09123456789');

    $response
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['mobile' => '09123456789']);
});

// تست‌های reusing existing person
it('reuses existing person with same mobile', function () {
    // ایجاد یک شخص موجود
    $existingPerson = Person::factory()->create([
        'name' => 'علی رضایی قدیمی',
        'mobile' => '09123456789',
        'postal_code' => '1111111111',
        'address' => 'آدرس قدیمی',
    ]);

    $payload = [
        'sender' => [
            'name' => 'علی رضایی جدید', // این نام باید نادیده گرفته شود
            'mobile' => '09123456789', // موبایل یکسان
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => '608850418600032250068114',
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response->assertStatus(201);

    // بررسی می‌کنیم که شخص جدیدی ایجاد نشده باشد
    expect(Person::where('mobile', '09123456789')->count())->toBe(1);

    // اطلاعات شخص موجود تغییر نکرده باشد
    $this->assertDatabaseHas('people', [
        'mobile' => '09123456789',
        'name' => 'علی رضایی قدیمی', // نام قدیمی باقی مانده
    ]);
});

// تست‌های اعتبارسنجی فرمت‌ها
it('validates mobile number format', function () {
    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => 'invalid-mobile', // فرمت نامعتبر
            'postal_code' => '1234567890',
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => '608850418600032250068114',
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['sender.mobile']);
});

it('validates postal code format', function () {
    $payload = [
        'sender' => [
            'name' => 'علی رضایی',
            'mobile' => '09123456789',
            'postal_code' => '12345', // فرمت نامعتبر
            'address' => 'تهران، خیابان ولیعصر',
        ],
        'receiver' => [
            'name' => 'محمد حسینی',
            'mobile' => '09351234567',
            'postal_code' => '0987654321',
            'address' => 'مشهد، بلوار وکیل آباد',
        ],
        'weight' => 2.5,
        'dimensions' => [
            'length' => 30,
            'width' => 20,
            'height' => 15,
        ],
        'tracking_code' => '608850418600032250068114',
    ];

    $response = $this->postJson('/api/parcels', $payload);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['sender.postal_code']);
});
