<?php

namespace App\Modules\Repositories\Interfaces;

use App\Models\Indicator;
use App\Models\Script;

interface Generator {
    public function execute(Indicator $indicator, int $preferredLevel=2, Script $defaultGenerator=null);
} 