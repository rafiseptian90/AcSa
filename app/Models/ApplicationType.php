<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationType extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];
    protected $table = 'app_types';
    public $incrementing = false;

    /*
     * Relations
     * */
    // to application
    public function applications(){
        return $this->hasMany(Application::class, 'app_type_id');
    }
}
