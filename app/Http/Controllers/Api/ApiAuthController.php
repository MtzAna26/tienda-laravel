<?php

namespace App\Http\Controllers\Api;  

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Vendedor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    // Registro de Cliente
    public function registrarCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'email' => 'required|email|unique:clientes',
            'password' => 'required|min:6',
        ]);

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['mensaje' => 'Cliente registrado correctamente'], 201);
    }

    // Registro de Vendedor
    public function registrarVendedor(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'email' => 'required|email|unique:vendedores',
            'password' => 'required|min:6',
        ]);

        $vendedor = Vendedor::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['mensaje' => 'Vendedor registrado correctamente'], 201);
    }

    // Inicio de sesión de Cliente
    public function loginCliente(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $cliente = Cliente::where('email', $request->email)->first();

        if (!$cliente || !Hash::check($request->password, $cliente->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        $token = $cliente->createToken('token-cliente')->plainTextToken;

        return response()->json(['token' => $token]);
    }
    // Inicio de Sesión de Vendedor
    public function loginVendedor(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $vendedor = Vendedor::where('email', $request->email)->first();

        if (!$vendedor || !Hash::check($request->password, $vendedor->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        $token = $vendedor->createToken('token-vendedor')->plainTextToken;

        return response()->json(['token' => $token]);
    }
    // Cerrar sesión
    public function logout(Request $request)
{
    // Verifica si el usuario está autenticado
    if ($request->user()) {
        $request->user()->tokens()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada']);
    }

    // Si no hay un usuario autenticado
    return response()->json(['error' => 'Usuario no autenticado'], 401);
}

}
