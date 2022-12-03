<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FormTip;

class Form extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [

        'full_name', 'number','email','intervals','mail','count','note','r_id','game_id','facebook_name','token','status_id','balance','redeem_status'

    ];

    public function tips(){
        return $this->hasMany(FormTip::class,'form_id','id');
    }
    public function activityStatus(){
        return $this->belongsTo(ActivityStatus::class, 'status_id');
    }
    public function unsubmail(){
        return $this->hasMany(Unsubmail::class, 'form_id','id')->orderBy('id','desc');
    }
}
