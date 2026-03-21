<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Service extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $table = 'service';
    public $timestamps = false;
    protected $primaryKey = 'service_id';
    protected $fillable = ['name', 'description', 'price', 'img_path', 'gallery_paths'];

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
        return $this->hasMany(Review::class, 'service_id', 'service_id');
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => (string) $this->name,
            'description' => (string) $this->description,
        ];
    }
}
