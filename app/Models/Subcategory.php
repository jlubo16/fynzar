<?php
// app/Models/Subcategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $fillable = ['category_id', 'name', 'keywords'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getKeywordsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setKeywordsAttribute($value)
    {
        $this->attributes['keywords'] = json_encode($value);
    }

    // Buscar subcategoría por palabra clave
    public static function findByKeyword($keyword)
    {
        return static::whereRaw('JSON_CONTAINS(keywords, ?)', [json_encode(strtolower($keyword))])
                    ->first();
    }
}