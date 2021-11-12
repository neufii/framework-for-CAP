<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\QuestionInstance;
use App\Models\Learner;

interface Updater {
    public function execute(QuestionInstance $questionInstance, Learner $learner, bool $isCorrect);
} 