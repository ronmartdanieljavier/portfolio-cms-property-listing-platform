<?php

namespace App\Modules\Properties\Models;

use Database\Factories\AmenityModelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class AmenityModel extends Model
{
    use HasFactory;

    protected $table = 'amenities';

    protected static function newFactory(): AmenityModelFactory
    {
        return AmenityModelFactory::new();
    }
}
