<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

/**
 * @property int $customer_id
 * @property string|null $title
 * @property string|null $fname
 * @property string|null $lname
 * @property string|null $addressline
 * @property string|null $town
 * @property string|null $zipcode
 * @property string|null $phone
 * @property int|null $user_id
 */
class Customer extends Model implements Searchable
{
    use HasFactory;
    protected $table = 'customer';
    public $timestamps = false;
    protected $primaryKey = 'customer_id';
    protected $fillable = ['title', 'fname', 'lname', 'addressline', 'town', 'zipcode', 'phone', 'user_id'];

    public function getSearchResult(): SearchResult
    {
        $url = route('customers.show', $this->customer_id);

        return new SearchResult(
            $this,
            $this->lname . " " . $this->fname,
            $url
        );
    }
}
