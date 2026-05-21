<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'book_id',
        'borrow_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date'    => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function return()
    {
        return $this->hasOne(ReturnBook::class);
    }
}
