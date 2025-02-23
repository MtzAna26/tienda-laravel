<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'vendedor_id'];

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
