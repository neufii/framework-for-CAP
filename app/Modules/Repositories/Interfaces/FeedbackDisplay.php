<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\QuestionInstance;

interface FeedbackDisplay {
    public function execute(QuestionInstance $questionInstance, bool $isCorrect, string $learnerAnswer);
} 