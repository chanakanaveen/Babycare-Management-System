<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightRecord extends Model
{
    use HasFactory;

    protected $table = 'weight_record';
    protected $primaryKey = 'record_id';

    protected $fillable = [
        'baby_id',
        'weight',
        'height',
        'head_circumference',
        'age_months',
        'milestones',
        'midwife_id',
        'record_date',
        'notes',
        'ai_prediction',
    ];

    protected $casts = [
        'ai_prediction' => 'array',
    ];

    public $timestamps = true;

    /**
     * Get the baby this record belongs to.
     */
    public function baby()
    {
        return $this->belongsTo(Baby::class, 'baby_id', 'baby_id');
    }

    /**
     * Get the midwife who recorded this.
     */
    public function midwife()
    {
        return $this->belongsTo(Midwife::class, 'midwife_id');
    }
}
