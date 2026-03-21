<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $orderinfo_id
 * @property int $customer_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $shipping_address
 * @property string $order_date
 * @property \Illuminate\Support\Carbon $date_placed
 * @property \Illuminate\Support\Carbon $date_shipped
 * @property float $shipping
 * @property string|null $discount_code
 * @property float $discount_amount
 * @property float $subtotal_amount
 * @property float $total_amount
 * @property string $payment_method
 * @property string $status
 */
class Order extends Model
{
    use HasFactory;
    protected $table = 'orderinfo';
    public $timestamps = false;
    protected $primaryKey = 'orderinfo_id';
}
