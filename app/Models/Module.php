<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    //relationship
    public function questionInstances(){
        //only for generator
        return $this->hasMany('App\Models\QuestionInstance', 'id');
    }

    public function compatibleIndicators(){
        return $this->belongsToMany('App\Models\Indicator', 'indicator_module', 'compatible_module_id', 'indicator_id');
    }

    //scope
    public function scopeActive($query){
        return $query->where('is_active', 1);
    }

    public function scopeLatest($query){
        return $query->orderByDesc('updated_at');
    }

    public function scopeGenerator($query){
        return $query->where('name', 'generator');
    }

    public function scopeSelector($query){
        return $query->where('name', 'selector');
    }

    public function scopeChecker($query){
        return $query->where('name', 'checker');
    }

    public function scopeQuestionDisplay($query){
        return $query->where('name', 'question_display');
    }

    public function scopeSolutionDisplay($query){
        return $query->where('name', 'solution_display');
    }

    public function scopeUpdater($query){
        return $query->where('name', 'updater');
    }

    public function scopeDistanceCalculator($query){
        return $query->where('name', 'distance_calculator');
    }

    //accessor
    public function getIsLatestAttribute(){
        $compatibleIndicatorsIds = $this->compatibleIndicators->pluck('id');
        $name = $this->name;
        return Module::whereHas('compatibleIndicators',function($q) use($name,$compatibleIndicatorsIds){
            $q->whereIn('indicator_id',$compatibleIndicatorsIds);
        })->where('name',$name)->orderByDesc('updated_at')->first()->id == $this->id;
    }
}
