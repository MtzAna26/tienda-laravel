<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarritoCompra;
use App\Models\DetalleCarrito;
use App\Models\Producto;

class CarritoController extends Controller
{
    // Agregar producto al carrito
    public function agregarProducto(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        // Obtener o crear carrito del cliente
        $carrito = CarritoCompra::firstOrCreate(
            ['cliente_id' => $request->cliente_id, 'estado' => 'pendiente']
        );

        // Verificar si el producto ya está en el carrito
        $detalle = DetalleCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $request->producto_id)
            ->first();

        if ($detalle) {
            $detalle->cantidad += $request->cantidad;
            $detalle->save();
        } else {
            DetalleCarrito::create([
                'carrito_id' => $carrito->id,
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad
            ]);
        }

        return response()->json(['message' => 'Producto agregado al carrito'], 200);
    }

    // Eliminar producto del carrito
    public function eliminarProducto(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'required|exists:productos,id'
        ]);

        $carrito = CarritoCompra::where('cliente_id', $request->cliente_id)
            ->where('estado', 'pendiente')
            ->first();

        if (!$carrito) {
            return response()->json(['message' => 'No hay carrito activo'], 404);
        }

        $detalle = DetalleCarrito::where('carrito_id', $carrito->id)
            ->where('producto_id', $request->producto_id)
            ->first();

        if ($detalle) {
            $detalle->delete();
            return response()->json(['message' => 'Producto eliminado del carrito'], 200);
        }

        return response()->json(['message' => 'Producto no encontrado en el carrito'], 404);
    }
}
