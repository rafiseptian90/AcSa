<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];
    protected $table = 'profiles';
    public $incrementing = false;

    /*
     * Relations
     * */

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
