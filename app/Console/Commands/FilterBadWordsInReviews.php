<?php

namespace App\Console\Commands;

use App\Models\Review;
use Illuminate\Console\Command;

class FilterBadWordsInReviews extends Command
{
    protected $signature = 'badwords:filter-existing {--dry-run : Preview changes without applying}';
    protected $description = 'Filter bad words from existing reviews in the database';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $reviews = Review::whereNotNull('comment')->get();
        $updated = 0;

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        foreach ($reviews as $review) {
            $original = $review->comment;
            $filtered = mask_bad_words($original);

            if ($original !== $filtered) {
                $updated++;
                $this->line("Review ID {$review->id}: Filtered");
                $this->line("  Before: {$original}");
                $this->line("  After:  {$filtered}");
                $this->line('');

                if (!$dryRun) {
                    $review->update(['comment' => $filtered]);
                }
            }
        }

        $this->line('');
        if ($dryRun) {
            $this->info("DRY RUN: Found {$updated} reviews with bad words");
            $this->comment("Run without --dry-run flag to apply changes");
        } else {
            $this->info("Successfully filtered {$updated} reviews!");
        }
    }
}
