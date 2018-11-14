<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    public $timestamps = true;
    protected $table = TBL_TASK_COMMENT;
    /**
     * @var array
     */
    protected $fillable = ['user_id','comments','task_priority','task_due_date','assing_task_id','comment_by_user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
}
