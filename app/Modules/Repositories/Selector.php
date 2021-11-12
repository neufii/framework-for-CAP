<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\Selector as SelectorInterface;

use App\Models\Indicator;
use App\Models\Learner;
use App\Models\QuestionInstance;

use App\Services\LearnerService;

class Selector implements SelectorInterface
{
    private $avgProbCorrect = 0.7; //average probability for answering correctly
    private $maxDistance = null; //maximum distance between leaner's current rating and selected instance's rating, null means no limit

    private function getPercentile(array $sortedArray, float $kth) {
        $idx = count($sortedArray) * $kth;
      
        if($idx != intval($idx)) {
            return $sortedArray[round($idx)-1];
        }
        else{
            return ($sortedArray[$idx-1] + $sortedArray[$idx])/2;
        }
    }
 
    public function execute(Learner $learner, Indicator $indicator, bool $includeHistory=false, int $preferredLevel=null){
        $historyQuery = $history = $learner->history()->where('indicator_id',$indicator->id);

        if($includeHistory){
            $answeredQuestions = $historyQuery->orderByDesc('history.created_at')->take(20)->pluck('question_id');
        }
        else{
            $answeredQuestions = $historyQuery->pluck('question_id');
        }

        $query = QuestionInstance::where('indicator_id',$indicator->id)->where('is_active',true);

        if($query->whereNotIn('id',$answeredQuestions)->get()->isEmpty() || ($query->get()->count() < 2)){
            //no question left or too few to calculate median
            $targetLevel = $preferredLevel ? $preferredLevel : 2; //if no preference, return 2 which is slightly easy question
            return ['targetLevel' => $targetLevel, 'questionInstance' => null];
        }

        $ratings = $query->orderBy('rating','asc')->pluck('rating')->toArray();
        $median = $this->getPercentile($ratings,0.5);
        $upperQuartile = $this->getPercentile($ratings,0.75);
        $lowerQuartile = $this->getPercentile($ratings,0.25);

        if(isset($preferredLevel)){
            switch($preferredLevel){
                case 1:{
                    $query = $query->where('rating','<=',$lowerQuartile);
                    break;
                }
                case 2:{
                    $query = $query->where('rating','>',$lowerQuartile)->where('rating','<=',$median);
                    break;
                }
                case 3:{
                    $query = $query->where('rating','>',$median)->where('rating','<=',$upperQuartile);
                    break;
                }
                case 4:{
                    $query = $query->where('rating','>',$upperQuartile);
                    break;
                }

                $selectedInstance = $query->inRandomOrder()->first();
                return ['targetLevel' => $preferredLevel, 'questionInstance' => $selectedInstance];
            }
        }
        else {
            $probCorrect = 0;
            $learnerStatistic = (new LearnerService($learner))->getStatistic($indicator);
            while($probCorrect<= 0.5 || $probCorrect >= 1){
                $x = mt_rand()/mt_getrandmax();
                $y = mt_rand()/mt_getrandmax();
                $probCorrect =  sqrt(-2*log($x))*cos(2*pi()*$y)*0.1 + $this->avgProbCorrect;
            }

            $targetRating = $learnerStatistic['rating'] - log($probCorrect/(1-$probCorrect));

            if(isset($this->maxDistance)){
                $query = $query->where('rating','>=',$learnerStatistic['rating'] - $this->maxDistance)
                    ->where('rating','<=',$learnerStatistic['rating'] + $this->maxDistance);
            }

            $selectedInstance = $query->orderByRaw('ABS(rating-'.$targetRating.') ASC')->first();

            if(isset($selectedInstance)){
                return ['targetLevel' => null, 'questionInstance' => $selectedInstance];
            }
            else{
                //calculate targetLevel from targetRating
                if($targetRating > $median){
                    $targetLevel = $targetRating > $upperQuartile ? 4:3;
                }
                else{
                    $targetLevel = $targetRating > $lowerQuartile ? 2:1;
                }

                return ['targetLevel' => $targetLevel, 'questionInstance' => null];
            }
        }
    }
}