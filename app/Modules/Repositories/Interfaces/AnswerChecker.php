<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\QuestionInstance;

interface AnswerChecker {
    public function execute(QuestionInstance $questionInstance, string $learnerAnswer);
} 