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
        'bmi',
        'birth_complications',
        'family_medical_history',
        'initial_observations',
        'allergies',
        'special_conditions',
        'midwife_id',
        'parent_id',
    ];

    public $timestamps = true;

    /**
     * Get the parent (guardian) of this baby.
     */
    public function parentUser()
    {
        return $this->belongsTo(ParentUser::class, 'parent_id');
    }

    /**
     * Get the midwife assigned to this baby.
     */
    public function midwife()
    {
        return $this->belongsTo(Midwife::class, 'midwife_id');
    }

    /**
     * Get all growth (weight) records for this baby.
     */
    public function growthRecords()
    {
        return $this->hasMany(WeightRecord::class, 'baby_id', 'baby_id');
    }

    /**
     * Get all vaccination records for this baby.
     */
    public function vaccinations()
    {
        return $this->hasMany(BabyVaccination::class, 'baby_id', 'baby_id');
    }
}
