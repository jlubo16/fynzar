<?php
// app/Models/Transaction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'description', 
        'category_id', 'subcategory_id', 'date', 'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    // Scope para gastos
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }

    // Scope para ingresos
    public function scopeIncomes($query)
    {
        return $query->where('type', 'income');
    }

    // Scope por mes y año
    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }
}