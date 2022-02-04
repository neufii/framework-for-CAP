<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\QuestionInstance;
use App\Models\Learner;

interface DistanceCalculator {
    public function execute(array $questionIds, float $threshold); //receive only question part of questionInstance
} 