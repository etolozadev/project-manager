<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id', 'client_id', 'name', 'start_date', 'end_date', 'status', 'notes'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function projectTasks()
    {
        return $this->hasMany(ProjectTask::class);
    }


}
