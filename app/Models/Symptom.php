<?php

namespace App\Models;

use Baum\Node;

class Symptom extends Node
{
    protected $table = 'symptom';

    public function diagnoses()
    {
        return $this->belongsToMany('App\Models\Diagnose', 'symptom_diagnose', 'symptom_id', 'diagnose_id')->whereLanguage(config('app.locale'));
    }
}
