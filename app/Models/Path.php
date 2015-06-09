<?php

namespace App\Models;

use Baum\Node;

class Path extends Node
{
    protected $table = 'path';

    public function diagnoses()
    {
        return $this->belongsToMany('App\Models\Diagnose', 'path_diagnose', 'path_id', 'diagnose_id');
    }
}
