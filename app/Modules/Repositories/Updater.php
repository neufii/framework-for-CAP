<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\Updater as UpdaterInterface;

use App\Models\QuestionInstance;
use App\Models\Learner;

use App\Services\LearnerService;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Updater implements UpdaterInterface {
 
    public function execute(QuestionInstance $questionInstance, Learner $learner, bool $isCorrect){
        $questionRating = $questionInstance->rating;
        $learnerService = new LearnerService($learner);
        $learnerStat = $learnerService->getStatistic($question->indicator);
        $learnerRating = $learnerStat->rating;
        $learnerAttempts = $learnerStat->total_attempts;
        $score = $isCorrect ? 1 : 0;

        $questionUncertaincy = 1/(1+(0.05*$questionInstance->totalAttempts));
        $learnerUncertaincy = 1/(1+(0.05*$learnerAttempts));
        
        $expectedScore = 1/(1+exp(-($learnerRating-$questionRating)));
        $newQuestionRating = $questionRating + $questionUncertaincy*($expectedScore - $score);
        $newLearnerRating = $learnerRating + $learnerUncertaincy*($score - $expectedScore);
        
        $newRatings['questionRating'] = $newQuestionRating;
        $newRatings['learnerRating'] = $newLearnerRating;

        return $newRatings;
    }
}