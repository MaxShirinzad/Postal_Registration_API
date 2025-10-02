markdown
# 📮 Postal Registration API - سامانه ثبت مرسوله پستی

یک سرویس REST API کامل برای ذخیره و نمایش مرسولات پستی با لاراول 12

## ✨ ویژگی‌ها

- ✅ ایجاد مرسوله جدید با کد رهگیری 24 رقمی
- ✅ اعتبارسنجی کامل داده‌ها (موبایل، کد پستی، کد رهگیری)
- ✅ مستندات Swagger/OpenAPI خودکار
- ✅ تست‌های کامل با Pest
- ✅ معماری نرمال‌سازی شده دیتابیس
- ✅ پاسخ‌های استاندارد JSON
- ✅ صفحه‌بندی و فیلتر پیشرفته

## 🛠 نیازمندی‌ها

- PHP 8.2+
- Composer
- MySQL 8.0+
- Laravel 12.x

## 🚀 نصب و راه‌اندازی

### 1. کلون کردن پروژه


git clone [repository-url]

cd parcel-api

### 2. نصب dependencyها 

composer install

### 3. تنظیم محیط

cp .env.example .env

php artisan key:generate

php artisan l5-swagger:generate

### 4. پیکربندی دیتابیس
فایل .env را ویرایش کنید:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parcel_api
DB_USERNAME=root
DB_PASSWORD=


### 5. اجرای migrations

php artisan migrate

### 6. تولید مستندات Swagger

php artisan l5-swagger:generate

## 🧪 اجرای تست‌ها
### اجرای تمام تست‌ها

php artisan test


### 📚 مستندات API

دسترسی به مستندات Swagger
پس از راه‌اندازی پروژه، مستندات API در آدرس زیر قابل دسترسی است:

http://localhost:8000/api/documentation

## تولید مجدد مستندات

php artisan l5-swagger:generate

## 🔌 Endpointهای API

## 1. ایجاد مرسوله جدید

Endpoint: POST /api/parcels

### Request Body:

json
{
  "sender": {
    "name": "علی رضایی",
    "mobile": "09123456789",
    "postal_code": "1234567890",
    "address": "تهران، خیابان ولیعصر"
  },
  "receiver": {
    "name": "محمد حسینی",
    "mobile": "09351234567",
    "postal_code": "0987654321",
    "address": "مشهد، بلوار وکیل آباد"
  },
  "weight": 2.5,
  "dimensions": {
    "length": 30,
    "width": 20,
    "height": 15
  },
  "tracking_code": "608850418600032250068114"
}


Response Success (201):


json
{
  "message": "Parcel created successfully",
  "data": {
    "id": 1,
    "tracking_code": "608850418600032250068114",
    "formatted_tracking_code": "6088-5041-8600-0322-5006-8114",
    "weight": 2.5,
    "dimensions": {
      "length": 30,
      "width": 20,
      "height": 15
    },
    "sender": {
      "name": "علی رضایی",
      "mobile": "09123456789",
      "postal_code": "1234567890",
      "address": "تهران، خیابان ولیعصر"
    },
    "receiver": {
      "name": "محمد حسینی",
      "mobile": "09351234567",
      "postal_code": "0987654321",
      "address": "مشهد، بلوار وکیل آباد"
    },
    "created_at": "2024-01-01 12:00:00",
    "updated_at": "2024-01-01 12:00:00"
  }
}

## 2. دریافت لیست مرسوله‌ها

Endpoint: GET /api/parcels

### Query Parameters:

page (اختیاری): شماره صفحه

per_page (اختیاری): تعداد در هر صفحه (پیش‌فرض: 15)

tracking_code (اختیاری): فیلتر بر اساس کد رهگیری

sender_mobile (اختیاری): فیلتر بر اساس موبایل فرستنده

receiver_mobile (اختیاری): فیلتر بر اساس موبایل گیرنده

Response (200):

json
{
  "data": [
    {
      "id": 1,
      "tracking_code": "608850418600032250068114",
      "formatted_tracking_code": "6088-5041-8600-0322-5006-8114",
      "weight": 2.5,
      "dimensions": {
        "length": 30,
        "width": 20,
        "height": 15
      },
      "sender": {
        "name": "علی رضایی",
        "mobile": "09123456789",
        "postal_code": "1234567890",
        "address": "تهران، خیابان ولیعصر"
      },
      "receiver": {
        "name": "محمد حسینی",
        "mobile": "09351234567",
        "postal_code": "0987654321",
        "address": "مشهد، بلوار وکیل آباد"
      },
      "created_at": "2024-01-01 12:00:00",
      "updated_at": "2024-01-01 12:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://localhost:8000/api/parcels?page=1",
    "last": "http://localhost:8000/api/parcels?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/parcels?page=2"
  }
}

## ⚠️ کدهای خطا

201 Created: مرسوله با موفقیت ایجاد شد

422 Unprocessable Entity: خطای اعتبارسنجی داده‌ها

404 Not Found: مرسوله یافت نشد


## 1. راه‌اندازی سرور


php artisan serve

## 2. نمونه درخواست‌ها
درخواست ایجاد مرسوله:

POST http://localhost:8000/api/parcels

Content-Type: application/json
Accept: application/json

{
  "sender": {
    "name": "علی رضایی",
    "mobile": "09123456789",
    "postal_code": "1234567890",
    "address": "تهران، خیابان ولیعصر"
  },
  "receiver": {
    "name": "محمد حسینی",
    "mobile": "09351234567",
    "postal_code": "0987654321",
    "address": "مشهد، بلوار وکیل آباد"
  },
  "weight": 2.5,
  "dimensions": {
    "length": 30,
    "width": 20,
    "height": 15
  },
  "tracking_code": "608850418600032250068114"
}


## 🔒 اعتبارسنجی‌ها

کد رهگیری
الزامی

دقیقاً 24 رقم

فقط اعداد

یکتا

موبایل
الزامی

فرمت: 09xxxxxxxxx

فرستنده و گیرنده نباید یکسان باشند

کد پستی
الزامی

دقیقاً 10 رقم

وزن و ابعاد
وزن: بین 0.1 تا 100 کیلوگرم

ابعاد: بین 1 تا 200 سانتی‌متر


## 🏗 معماری پروژه



app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── ParcelController.php
│   ├── Requests/
│   │   └── StoreParcelRequest.php
│   └── Resources/
│       ├── ParcelResource.php
│       └── ParcelCollection.php
├── Models/
│   ├── Parcel.php
│   └── Person.php
tests/
├── Feature/
│   └── ParcelApiTest.php
├── Unit/
│   ├── ParcelModelTest.php
│   └── PersonModelTest.php
database/
├── migrations/
│   ├── create_people_table.php
│   └── create_parcels_table.php
└── factories/
    ├── ParcelFactory.php
    └── PersonFactory.php
	
	
	
	
## 🚀 Deploy

روی سرور معمولی

# تنظیم permissions

chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Optimize

php artisan optimize
php artisan config:cache
php artisan route:cache

# Migration در production

php artisan migrate --force


## 📄 لایسنس
این پروژه تحت لایسنس MIT منتشر شده است.

## 📞 پشتیبانی
اگر سوال یا مشکلی داشتید، لطفاً یک Issue در GitHub ایجاد کنید.

توسعه داده شده با ❤️ و لاراول

## Max Shirinzad


