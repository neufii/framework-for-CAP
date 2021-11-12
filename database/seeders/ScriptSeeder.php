<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Script;

class ScriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create new modules
        $generator = new Script();
        $generator->name = 'generator';
        $generator->path = '/Users/neufii/Documents/M.Eng/IIAFramework/app/Modules/Scripts/generator1.py';
        $generator->type = 'python3';
        $generator->save();
        $generator->compatibleIndicators()->sync([1]);

        $generator = new Script();
        $generator->name = 'generator';
        $generator->path = '/Users/neufii/Documents/M.Eng/IIAFramework/app/Modules/Scripts/generator2.py';
        $generator->type = 'python3';
        $generator->save();
        $generator->compatibleIndicators()->sync([2]);

        $checker = new Script();
        $checker->name = 'checker';
        $checker->path = '/Users/neufii/Documents/M.Eng/IIAFramework/app/Modules/Scripts/answerChecker.py';
        $checker->type = 'python3';
        $checker->save();
        $checker->compatibleIndicators()->sync([1,2]);

        $questionDisplay = new Script();
        $questionDisplay->name = 'question_display';
        $questionDisplay->path = '/Users/neufii/Documents/M.Eng/IIAFramework/app/Modules/Scripts/questionDisplay.py';
        $questionDisplay->type = 'python3';
        $questionDisplay->save();
        $questionDisplay->compatibleIndicators()->sync([1,2]);

        $solutionDisplay = new Script();
        $solutionDisplay->name = 'feedback_display';
        $solutionDisplay->path = '/Users/neufii/Documents/M.Eng/IIAFramework/app/Modules/Scripts/feedbackDisplay.py';
        $solutionDisplay->type = 'python3';
        $solutionDisplay->save();
        $solutionDisplay->compatibleIndicators()->sync([1,2]);

        $distanceCalculator = new Script();
        $distanceCalculator->name = 'distance_calculator';
        $solutionDisplay->path = '/Users/neufii/Documents/M.Eng/IIAFramework/app/Modules/Scripts/distanceCalculator.py';
        $distanceCalculator->type = 'python3';
        $distanceCalculator->save();
        $distanceCalculator->compatibleIndicators()->sync([1,2]);
    }
}
