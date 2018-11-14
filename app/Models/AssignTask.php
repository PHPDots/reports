<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignTask extends Model
{
    public $timestamps = true;
    protected $table = TBL_ASSIGN_TASK;
    /**
     * @var array
     */
    protected $fillable = ['user_id','project_id','title','description','priority','due_date','status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
     
}
