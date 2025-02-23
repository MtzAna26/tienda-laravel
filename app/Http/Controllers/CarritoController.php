<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarritoCompra;
use App\Models\DetalleCarrito;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Tienda;
use Illuminate\Support\Facades\Redis;

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

    public function finalizarCompra(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        // Obtener el carrito del cliente
        $carrito = CarritoCompra::where('cliente_id', $request->cliente_id)
            ->where('estado', 'pendiente')
            ->first();

        if (!$carrito) {
            return response()->json(['message' => 'No hay carrito activo'], 404);
        }

        $detalles = DetalleCarrito::where('carrito_id', $carrito->id)->get();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], 400);
        }

        $total = 0;

        // Verificar stock y calcular total
        foreach ($detalles as $detalle) {
            $producto = Producto::find($detalle->producto_id);

            if ($producto->stock < $detalle->cantidad) {
                return response()->json([
                    'message' => "Stock insuficiente para el producto: {$producto->nombre}"
                ], 400);
            }

            $total += $producto->precio * $detalle->cantidad;
        }

        // Registrar la compra
        $compra = Compra::create([
            'cliente_id' => $request->cliente_id,
            'fecha' => now(),
            'total' => $total
        ]);

        // Registrar detalles de compra y actualizar stock
        foreach ($detalles as $detalle) {
            $producto = Producto::find($detalle->producto_id);

            DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
                'cantidad' => $detalle->cantidad,
                'subtotal' => $producto->precio * $detalle->cantidad
            ]);

            // Descontar stock
            $producto->stock -= $detalle->cantidad;
            $producto->save();
        }

        // Marcar carrito como finalizado
        $carrito->estado = 'finalizado';
        $carrito->save();

        // Vaciar carrito
        DetalleCarrito::where('carrito_id', $carrito->id)->delete();

        return response()->json(['message' => 'Compra finalizada con éxito', 'compra_id' => $compra->id], 200);
    }

    public function historialCompras(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        // Obtener todas las compras del cliente
        $compras = Compra::where('cliente_id', $request->cliente_id)
            ->with('detalles.producto') // Cargar detalles de la compra y productos
            ->get();

        if ($compras->isEmpty()) {
            return response()->json(['message' => 'No se encontraron compras para este cliente'], 404);
        }

        return response()->json(['compras' => $compras], 200);
    }


    public function historialVentas(Request $request)
    {
        $request->validate([
            'vendedor_id' => 'required|exists:vendedores,id'
        ]);

        // Obtener la tienda del vendedor
        $tienda = Tienda::where('id_vendedor', $request->vendedor_id)->first();

        if (!$tienda) {
            return response()->json(['message' => 'El vendedor no tiene una tienda registrada'], 404);
        }

        // Obtener todas las compras relacionadas con los productos de la tienda
        $ventas = Compra::whereHas('detalles.producto', function ($query) use ($tienda) {
            $query->where('id_tienda', $tienda->id);
        })
            ->with(['detalles.producto', 'detalles.producto.tienda']) // Cargar detalles de compra y productos con tienda
            ->get();

        if ($ventas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron ventas para esta tienda'], 404);
        }

        return response()->json(['ventas' => $ventas], 200);
    }
}
