<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',       // 'expense' или 'income'
        'parent_id',  // для вложенных категорий
        'icon',       // название иконки, если есть
    ];

    /**
     * Категория принадлежит пользователю
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Родительская категория (nullable)
     */
     public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Дочерние категории
     */
    public function childCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Транзакции, относящиеся к категории
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
