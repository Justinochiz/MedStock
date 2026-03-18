<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Item extends Model implements Searchable
{
    use HasFactory, SoftDeletes;
    protected $table = 'item';
    public $timestamps = false;
    protected $primaryKey = 'item_id';
    protected $fillable = ['description', 'category', 'cost_price', 'sell_price', 'img_path', 'gallery_paths'];

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

    public function getSearchResult(): SearchResult
    {
        $url = route('items.show', $this->item_id);

        return new SearchResult(
            $this,
            $this->description,
            $url
        );
    }
}
