<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'published_date',
        'user_borrowed_id',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class,'book_category','book_id','category_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class,'book_author','book_id','author_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function followers(){
        return $this->belongsToMany(User::class,'notifies','book_id','user_id');
    }
}
