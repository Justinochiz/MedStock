<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Review;
use App\Services\BadWordsFilter;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all reviews with comments and apply filter
        Review::whereNotNull('comment')->get()->each(function ($review) {
            $filteredComment = BadWordsFilter::mask($review->comment);
            if ($filteredComment !== $review->comment) {
                $review->update([
                    'comment' => $filteredComment
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed
        // Bad word masking is one-way
    }
};
