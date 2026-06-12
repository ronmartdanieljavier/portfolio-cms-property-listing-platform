<?php

namespace App\Modules\Users\Enums;

enum UserRoleEnum: string
{
    case Admin = 'admin';
    case Agent = 'agent';
}
