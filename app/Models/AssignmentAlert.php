<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_noted',
        'project_id',
        'user_id',
    ];

    protected $with = ['project'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
