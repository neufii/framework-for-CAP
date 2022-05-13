<?php

namespace App\Services;

use App\Models\Indicator;
use App\Models\Learner;

class LearnerService
{
    private $learner;

    public function __construct(Learner $learner=null){
        $this->learner = $learner;
    }

    public static function register(){
        $learner = new Learner();
        $learner->save();
        return $learner;
    }

    private function addNewIndicator(Indicator $indicator){
        $this->learner->learningIndicators()->attach($indicator,['rating' => 0 ,'total_attempts' => 0]);
    }

    public function getStatistic(Indicator $indicator){
        if(!$this->learner->learningIndicators()->where('indicator_id',$indicator->id)->first()){
            $this->addNewIndicator($indicator);
        }

        $stat = $this->learner->learningIndicators()->where('indicator_id',$indicator->id)->first()->pivot;
        $correctAttempts = $this->learner->history()->where('indicator_id',$indicator->id)->wherePivot('is_correct',1)->count();
        $avgTime = $this->learner->history()->where('indicator_id',1)->avg('time_used');
        $uniqueQuestionTotalAttempts = $this->learner->history()->where('indicator_id',1)->get()->unique('id')->count();

        return [
            'rating' => $stat->rating, 
            'total_attempts' => $stat->total_attempts,
            'unique_question_total_attempts' => $uniqueQuestionTotalAttempts,
            'correct_attempts' => $correctAttempts,
            'average_time_used' => $avgTime
        ];
    }
}