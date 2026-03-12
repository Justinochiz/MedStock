<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $orderinfo_id
 * @property string $customer_name
 * @property string $order_date
 * @property string $status
 */
class Order extends Model
{
    use HasFactory;
    protected $table = 'orderinfo';
    public $timestamps = false;
    protected $primaryKey = 'orderinfo_id';
}
