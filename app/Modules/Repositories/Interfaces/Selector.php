<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\Indicator;
use App\Models\Learner;

interface Selector {
    public function execute(Learner $learner, Indicator $indicator, bool $includeHistory=false, int $preferredLevel=null);
} 