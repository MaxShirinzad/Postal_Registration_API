<?php

namespace App\Http\Requests\Parcel;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreParcelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender.name' => 'required|string|max:255',
            'sender.mobile' => [
                'required',
                'string',
                'regex:/^09[0-9]{9}$/'
            ],
            'sender.postal_code' => 'required|string|regex:/^\d{10}$/',
            'sender.address' => 'required|string|max:500',

            'receiver.name' => 'required|string|max:255',
            'receiver.mobile' => [
                'required',
                'string',
                'regex:/^09[0-9]{9}$/',
                'different:sender.mobile'
            ],
            'receiver.postal_code' => 'required|string|regex:/^\d{10}$/',
            'receiver.address' => 'required|string|max:500',

            'weight' => 'required|numeric|min:0.1|max:100',
            'dimensions.length' => 'required|numeric|min:1|max:200',
            'dimensions.width' => 'required|numeric|min:1|max:200',
            'dimensions.height' => 'required|numeric|min:1|max:200',

            // اضافه کردن rule برای tracking_code اختیاری
            'tracking_code' => 'nullable|string|max:50|unique:parcels,tracking_code',
        ];
    }

    public function messages(): array
    {
        return [
            'sender.mobile.regex' => 'فرمت شماره موبایل فرستنده صحیح نیست',
            'receiver.mobile.regex' => 'فرمت شماره موبایل گیرنده صحیح نیست',
            'receiver.mobile.different' => 'شماره موبایل گیرنده باید با فرستنده متفاوت باشد',
            'sender.postal_code.regex' => 'فرمت کد پستی فرستنده باید 10 رقمی باشد',
            'receiver.postal_code.regex' => 'فرمت کد پستی گیرنده باید 10 رقمی باشد',
            'tracking_code.unique' => 'این کد رهگیری قبلاً استفاده شده است',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
