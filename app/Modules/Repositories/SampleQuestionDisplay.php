<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\QuestionDisplay as QuestionDisplayInterface;

use App\Models\QuestionInstance;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SampleQuestionDisplay implements QuestionDisplayInterface {
 
    public function execute(QuestionInstance $questionInstance){
        $displayScript = $questionInstance->indicator()->first()->compatibleScripts()->questionDisplay()->active()->latest()->first();

        if(!isset($displayScript)){
            return null;
        }
        
        $scriptType = $displayScript->type;
        $processArray = [$scriptType, $displayScript->path, $questionInstance->question];

        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $questionScript = $process->getOutput();

        return $questionScript;
    }
}