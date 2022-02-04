<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    use HasFactory;

    // protected $fillable = ['user_id'];

    //relationship
    public function history(){
        return $this->belongsToMany('App\Models\QuestionInstance', 'history', 'learner_id', 'question_id')
        ->withPivot('answer', 'is_correct', 'time_used')
        ->withTimestamps();
    }

    public function learningIndicators(){
        return $this->belongsToMany('App\Models\Indicator', 'indicator_learner', 'learner_id', 'indicator_id')
        ->withPivot('total_attempts', 'rating')
        ->withTimestamps();
    }
}
