<?php

namespace App\Modules\Repositories;

use App\Models\Indicator;
use App\Models\Script;

use App\Modules\Repositories\Interfaces\Generator as GeneratorInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SampleGenerator implements GeneratorInterface {
 
    public function execute(Indicator $indicator, int $preferredLevel=2, Script $defaultGenerator=null){
        $generatorScript = $defaultGenerator ? $defaultGenerator : $indicator->compatibleScripts()->generator()->active()->latest()->first();

        if(!isset($generatorScript)){
            return null;
        }
        
        $scriptType = $generatorScript->type;
        $processArray = [$scriptType, $generatorScript->path, $preferredLevel];

        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $rawQuestion = $process->getOutput();
        $question = json_decode($rawQuestion,true);

        $question['script_id'] = $generatorScript->id;

        return $question;
    }
}