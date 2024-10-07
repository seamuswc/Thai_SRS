<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Thai extends Model
{
    use HasFactory;

    protected $fillable = ['word', 'meaning', 'pronunciation', 'interval', 'repetitions', 'easeFactor', 'nextReviewDate', 'mastered'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
