<?php

namespace App\Modules\Properties\Enums;

enum PropertyTypeEnum: string
{
    case House = 'house';
    case Apartment = 'apartment';
    case Condo = 'condo';
    case Townhouse = 'townhouse';
    case Land = 'land';
    case Commercial = 'commercial';
}
