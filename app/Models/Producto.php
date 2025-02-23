<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'tienda_id'];

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    public function detallesCompra(){
        return $this->hasMany(DetalleCompra::class);
    }
}
