<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\DistanceCalculator as DistanceCalculatorInterface;

use App\Models\QuestionInstance;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SampleDistanceCalculator implements DistanceCalculatorInterface {
 
    public function execute(array $questionIds){
        return;
    }
}