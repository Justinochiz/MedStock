# Bad Words Filter System

This system automatically filters and masks bad words from user input across your application.

## Files Created

- `config/badwords.php` - Configuration for bad words filter
- `app/Services/BadWordsFilter.php` - Filter service with multiple methods
- `app/Helpers/BadWordsHelper.php` - Helper functions for easy access

## Usage

### Option 1: Using Helper Functions (Recommended)

```php
// Mask bad words (replace with asterisks)
$cleanText = mask_bad_words($userInput);

// Check if text contains bad words
if (has_bad_words($userInput)) {
    // Handle forbidden words
}

// Extract bad words found in text
$foundBadWords = extract_bad_words($userInput);
```

### Option 2: Using Service Class

```php
use App\Services\BadWordsFilter;

// Mask bad words
$cleanText = BadWordsFilter::mask($userInput);

// Check if contains bad words
$hasBadWords = BadWordsFilter::contains($userInput);

// Extract bad words
$badWords = BadWordsFilter::extract($userInput);
```

## Integration Examples

### In Review Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Automatically mask bad words before saving
            $model->comment = mask_bad_words($model->comment);
        });

        static::updating(function ($model) {
            // Mask bad words on update
            $model->comment = mask_bad_words($model->comment);
        });
    }
}
```

### In Controller

```php
namespace App\Http\Controllers;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Check for bad words
        if (has_bad_words($validated['comment'])) {
            return back()->with('warning', 'Your comment contains inappropriate language. It has been filtered.');
        }

        // Mask any bad words before saving
        $validated['comment'] = mask_bad_words($validated['comment']);

        Review::create($validated);

        return back()->with('success', 'Review created successfully!');
    }
}
```

### In Blade Template

```blade
<!-- Display cleaned text -->
{{ mask_bad_words($review->comment) }}

<!-- Or if already stored cleaned -->
{{ $review->comment }}
```

## Customizing Bad Words

Edit the regex pattern in `app/Services/BadWordsFilter.php` to add more words:

```php
$pattern = '/\b(?:fuck\w*|shit\w*|bitch\w*|asshole|damn(?:ed|ing)?|YOURWORD)\b/i';
```

## Methods Available

| Method | Description | Returns |
|--------|-------------|---------|
| `mask($text)` | Replace bad words with asterisks | string or null |
| `contains($text)` | Check if text has bad words | boolean |
| `extract($text)` | Get array of bad words found | array |

## After Installation

Run this command to regenerate autoloader:

```bash
composer dump-autoload
```
