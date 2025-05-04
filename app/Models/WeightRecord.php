<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightRecord extends Model
{
    use HasFactory;

    protected $table = 'weight_record';

    protected $fillable = [
        'baby_id',
        'weight',
        'height',
        'head_circumference',
        'midwife_id',
        'record_date',
        'notes',
    ];
    public $timestamps = true; // Enable timestamps for created_at and updated_at
}
