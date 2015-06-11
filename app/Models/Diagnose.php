<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnose extends Model
{
    protected $table = 'diagnose';

    protected $fillable = ['name', 'definition', 'checklist', 'content', 'page'];

}
