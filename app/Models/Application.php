<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['uuid'];
    protected $table = 'apps';
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    /*
     * Relations
     * */
    public function type(){
        return $this->belongsTo(ApplicationType::class, 'app_type_id');
    }
}
