<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

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
}
