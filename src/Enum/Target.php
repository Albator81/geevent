<?php

namespace App\Enum;

enum Target: string
{
    case ALL = 'Tout le monde';
    case MEMBERS = 'Uniquement les membres';
}
