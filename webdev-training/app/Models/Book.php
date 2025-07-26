<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
     protected $fillable = [
        'user_id',
        'name',
        'harga',
        'stock',
        'image',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/books/' . $image),
        );
    }

}
