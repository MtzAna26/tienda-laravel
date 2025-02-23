<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleCarrito extends Model
{
    use HasFactory;

    protected $fillable = ['carrito_id', 'producto_id', 'cantidad'];

    public function carrito()
    {
        return $this->belongsTo(CarritoCompra::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
