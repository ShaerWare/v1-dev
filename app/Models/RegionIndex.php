<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionIndex extends Model
{
    use HasFactory;

    protected $fillable = ['index_code', 'name'];
}
