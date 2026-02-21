<?php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'color', 'order'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope para gastos (excluye ingresos si es necesario)
    public function scopeForExpenses($query)
    {
        return $query->where('name', '!=', 'Ingresos');
    }
}