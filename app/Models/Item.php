<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Item extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    protected $table = 'item';
    public $timestamps = false;
    protected $primaryKey = 'item_id';
    protected $fillable = ['description', 'category', 'brand', 'cost_price', 'sell_price', 'img_path', 'gallery_paths'];

    protected $casts = [
        'gallery_paths' => 'array',
    ];

    public function imageGallery(): array
    {
        $gallery = $this->gallery_paths ?? [];

        if (!is_array($gallery)) {
            $gallery = [];
        }

        if (empty($gallery) && !empty($this->img_path)) {
            $gallery = [$this->img_path];
        }

        return $gallery;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'item_id', 'item_id');
    }

    public function toSearchableArray(): array
    {
        return [
            'description' => (string) $this->description,
            'category' => (string) $this->category,
            'brand' => (string) ($this->brand ?? ''),
        ];
    }
}
