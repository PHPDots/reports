<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Cviebrock\EloquentSluggable\Sluggable;

class Department extends Model
{
    public $timestamps = true;
    protected $table = TBL_DEPARTMENT;
   
    protected $fillable = ['title'];
    
      
}
