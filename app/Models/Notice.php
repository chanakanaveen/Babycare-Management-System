<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $table = 'notice';

    protected $primaryKey = 'notice_id';

    protected $fillable = [
        'title',
        'content',
        'sender_type',
        'sender_id',
        'notice_type',
        'target_group',
        'created_at',
        'scheduled_at',
        'expires_at',
    ];

    public $timestamps = false;

}
