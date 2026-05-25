<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model

{

 use HasFactory;
    protected $fillable = [
        'order_no',
        'customer_name',
        'customer_email',
        'product_name',
        'price'
    ];

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function lastEmailLog()
    {
        return $this->hasOne(EmailLog::class)->latest();
    }
}