<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Vendedor extends Model
{
    use HasApiTokens, HasFactory;
    protected $fillable = ['nombre', 'email', 'password'];
    protected $hidden = ['password'];
    protected $table = 'vendedores';
}
