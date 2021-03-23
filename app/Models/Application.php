<?php

namespace App\Models;

use App\Libs\Response;
use App\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory, Uuid, Filterable;

    protected $guarded = ['id'];
    protected $table = 'apps';
    protected $hidden = ['app_type_id', 'id', 'created_at', 'updated_at'];
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
    public function setNameAttribute($name){
        $this->attributes['slug'] = Str::slug($name);
        return $this->attributes['name'] = $name;
    }

    /*
     * Relations
     * */
    // to application type
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApplicationType::class, 'app_type_id');
    }

    // to accounts
    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class, 'app_id');
    }
}
