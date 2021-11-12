<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\QuestionInstance;

interface QuestionDisplay {
    public function execute(QuestionInstance $questionInstance);
} 