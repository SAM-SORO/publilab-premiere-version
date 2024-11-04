<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdIndexation extends Model
{
    use HasFactory;
    protected $fillable = ['nomBDInd'];
}

// php artisan make:model BdIndexation -m
