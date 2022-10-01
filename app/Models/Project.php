<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Project extends Model
{
    use HasFactory;

    const OPEN_STATUS = 0;
    const CLOSED_STATUS = 1;

    protected $fillable = [
        'title',
        'description',
        'deadline',
        'status',
        'client_id',
        'user_id',
    ];

    public function getFormattedDeadlineAttribute()
    {
        return (new \DateTime($this->deadline))->format('m/d/Y');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
