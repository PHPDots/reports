<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareLicense extends Model
{
    public $timestamps = true;
    protected $table = TBL_SOFTWARE_LICENSE;

    /**
     * @var array
     */
    protected $fillable = ['title', 'url','download_link','license_key','expiry_date','payment_type'];
}
