<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'rut', 'address'
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
