<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpinnerWinner extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name', 'form_id'

    ];
    
    public function form(){
        return $this->belongsTo(Form::class,'id','form_id');
    }
}
