<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tienda;

class TiendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Tienda::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'vendedor_id'  => 'required|exists:vendedores,id',
        ]);
        return Tienda::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Tienda $tienda)
    {
        return $tienda;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tienda $tienda)
    {
        $tienda->update($request->all());
        return $tienda;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tienda $tienda)
    {
        $tienda->delete();
        return response()->json(['menssage' => 'Tienda eliminada.']);
    }
}
