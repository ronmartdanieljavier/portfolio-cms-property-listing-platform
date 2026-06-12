<?php

namespace App\Modules\Properties\Models;

use App\Modules\Properties\Enums\PropertyStatusEnum;
use App\Modules\Properties\Enums\PropertyTypeEnum;
use Database\Factories\PropertyModelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agent_id',
    'title',
    'description',
    'price',
    'property_type',
    'status',
    'bedrooms',
    'bathrooms',
    'floor_area',
    'lot_area',
    'floors',
    'address',
    'city',
    'province',
    'country',
    'zip_code',
    'latitude',
    'longitude',
])]
class PropertyModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'properties';

    protected static function newFactory(): PropertyModelFactory
    {
        return PropertyModelFactory::new();
    }

    /**
     * @return BelongsToMany<AmenityModel, $this, Pivot>
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(AmenityModel::class, 'amenity_property', 'property_id', 'amenity_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'property_type' => PropertyTypeEnum::class,
            'status' => PropertyStatusEnum::class,
            'price' => 'decimal:2',
            'floor_area' => 'decimal:2',
            'lot_area' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }
}
