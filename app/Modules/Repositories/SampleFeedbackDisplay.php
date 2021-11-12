<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\FeedbackDisplay as FeedbackDisplayInterface;

use App\Models\QuestionInstance;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SampleFeedbackDisplay implements FeedbackDisplayInterface {
 
    public function execute(QuestionInstance $questionInstance, bool $isCorrect, string $learnerAnswer){
        $displayScript = $questionInstance->indicator()->first()->compatibleScripts()->feedbackDisplay()->active()->latest()->first();

        if(!isset($displayScript)){
            return null;
        }
        
        $scriptType = $displayScript->type;
        $processArray = [$scriptType, $displayScript->path, $questionInstance->solution];

        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $questionScript = $process->getOutput();

        return $questionScript;
    }
}