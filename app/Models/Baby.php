<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baby extends Model
{
    use HasFactory;
    protected $table = 'baby';
    protected $primaryKey = 'baby_id';

    protected $fillable = [
        'full_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'birth_hospital',
        'birth_weight',
        'parent_id',
    ];

    public $timestamps = false;

}
