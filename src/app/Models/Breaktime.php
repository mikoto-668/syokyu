<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaktime extends Model
{
    protected $fillable = [
        'id',
        'time_id',
        'breakIn',
        'breakOut',
        'breaktime',
        ];
}
