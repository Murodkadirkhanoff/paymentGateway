<?php

namespace App\Helpers;

class PaymentStatus
{
    const STATUS_NEW = 'new';
    const STATUS_CREATED = 'created';
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PAID = 'paid';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REJECTED = 'rejected';
}
