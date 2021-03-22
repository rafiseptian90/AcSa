<?php

namespace App\Models;

use App\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApplicationType extends Model
{
    use HasFactory, Uuid, Filterable;

    protected $guarded = ['id'];
    protected $table = 'app_types';
    public $incrementing = false;

    /*
     * Scope
     * */
    public function scopeOrderedAsc($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /*
     * Mutators
     * */
    // set slug by name
    public function setNameAttribute($name){
        $this->attributes['slug'] = Str::slug($name);

        return $this->attributes['name'] = $name;
    }

    /*
     * Relations
     * */
    // to application
    public function applications(){
        return $this->hasMany(Application::class, 'app_type_id');
    }
}
