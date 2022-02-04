<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Modules\ModuleManager;

class QuestionInstance extends Model
{
    use HasFactory;

    //relationship
    public function indicator(){
        return $this->belongsTo('App\Models\Indicator', 'indicator_id', 'id');
    }

    public function history(){
        return $this->belongsToMany('App\Models\Learner', 'history', 'question_id', 'learner_id')
        ->withPivot('answer', 'is_correct', 'time_used')
        ->withTimestamps();
    }

    public function generator(){
        return $this->belongsTo('App\Models\Script', 'generator_script_id', 'id');
    }
}
