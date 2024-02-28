<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSku extends Pivot
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = ['order_id','sku_id','product','quantity','unitary_price'];

    /**
     * @var string[]
     */
    protected $casts = ['product' => 'json'];

    /**
     * @return BelongsTo
     */
    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function sku():BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }
}
