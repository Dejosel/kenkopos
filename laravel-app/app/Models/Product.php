<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Product — KenkoPOS (Laravel)
 *
 * Representa un producto del catálogo.
 * Equivalente a la clase Product del módulo PHP puro (v1.x).
 *
 * @property int    $product_id
 * @property string $name
 * @property string $sku
 * @property float  $price
 * @property \Carbon\Carbon $created_at
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'products';

    /**
     * Clave primaria personalizada (compatible con v1.x).
     */
    protected $primaryKey = 'product_id';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'name',
        'sku',
        'price',
    ];

    /**
     * Casteo de tipos de datos.
     */
    protected $casts = [
        'price'      => 'decimal:2',
        'created_at' => 'datetime',
    ];
}
