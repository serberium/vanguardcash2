<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['cash_register_id', 'total_amount', 'received_amount', 'change_amount', 'payment_method', 'is_internal_consumption'];

    public function cashRegister() {
        return $this->belongsTo(CashRegister::class);
    }

    public function items() {
        return $this->hasMany(SaleItem::class);
    }
}