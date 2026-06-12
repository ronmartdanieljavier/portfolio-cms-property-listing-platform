<?php

namespace App\Modules\Properties\Enums;

enum PropertyStatusEnum: string
{
    case ForSale = 'for_sale';
    case ForRent = 'for_rent';
    case Sold = 'sold';
    case Rented = 'rented';
}
