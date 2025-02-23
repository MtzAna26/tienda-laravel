<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarritoCompra extends Model
{
    use HasFactory;
    
    protected $fillable = ['cliente_id', 'estado']; 

    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class);
    }
}
