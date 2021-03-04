<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['uuid'];
    protected $table = 'accounts';
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    /*
     * Relations
     * */
    public function app(){
        return $this->belongsTo(Application::class, 'app_id');
    }
}
