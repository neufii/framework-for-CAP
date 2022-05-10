<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\QuestionInstance;
use App\Models\Learner;
use App\Models\Indicator;

interface DistanceCalculator {
    public function execute(array $questionIds, float $threshold, Indicator $indicator); //receive only question part of questionInstance
} 