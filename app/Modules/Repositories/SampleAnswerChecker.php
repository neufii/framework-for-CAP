<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\AnswerChecker as AnswerCheckerInterface;

use App\Models\QuestionInstance;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SampleAnswerChecker implements AnswerCheckerInterface {
 
    public function execute(QuestionInstance $questionInstance, string $learnerAnswer){
        return $questionInstance->answer == $learnerAnswer;
    }
}