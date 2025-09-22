<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'parent_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Отношение к родительской категории
     */
    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    /**
     * Отношение к дочерним категориям
     */
    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    /**
     * Отношение к услугам
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * Scope для активных категорий
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
