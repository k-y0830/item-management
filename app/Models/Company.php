<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Order;

class Company extends Model
{
    use HasFactory;

    public function items() {
        return $this->hasMany(Item::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }
    
    protected $fillable = [
        'name',
        'address',
        'tell',
    ];
    
}
