<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'brand',
        'description',
        'price',
        'category_ids',
        'condition',
        'status',
        'image_path',
    ];

    protected $casts = [
        'category_ids' => 'array',
    ];

    public function getCategoriesListAttribute()
    {
        return Category::whereIn('id', $this->category_ids ?? [])->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id')
            ->withTimestamps();
    }

    public function isLikedBy($user)
    {
        if (!$user) {
            return false;
        }
        return $this->likedUsers()->where('user_id', $user->id)->exists();
    }

    public function getConditionLabelAttribute()
    {
        $labels = [
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い',
        ];

        return $labels[$this->condition] ?? '不明';
    }

    public function getImageUrlAttribute()
    {
        return Str::startsWith($this->image_path, ['http://', 'https://'])
            ? $this->image_path
            : asset('storage/' . $this->image_path);
    }
}
