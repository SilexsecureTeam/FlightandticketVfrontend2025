<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PROCESSING = 'PROCESSING';
    case CONFIRMED = 'CONFIRMED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    case FAILED = 'FAILED';
}
