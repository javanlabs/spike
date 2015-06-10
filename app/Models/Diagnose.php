<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnose extends Model
{
    protected $table = 'diagnose';

    protected $fillable = ['name', 'content', 'page'];
}
