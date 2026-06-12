<?php

namespace App\Modules\Properties\Http\Requests;

use App\Modules\Properties\Enums\PropertyStatusEnum;
use App\Modules\Properties\Enums\PropertyTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'propertyType' => ['sometimes', new Enum(PropertyTypeEnum::class)],
            'status' => ['sometimes', new Enum(PropertyStatusEnum::class)],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:255'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:255'],
            'floorArea' => ['nullable', 'numeric', 'min:0'],
            'lotArea' => ['nullable', 'numeric', 'min:0'],
            'floors' => ['nullable', 'integer', 'min:1', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:255'],
            'state' => ['sometimes', 'string', 'max:255'],
            'country' => ['sometimes', 'string', 'size:2'],
            'postcode' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
