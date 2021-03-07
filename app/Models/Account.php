<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];
    protected $table = 'accounts';
    public $incrementing = false;

    /*
     * Relations
     * */
    // to application
    public function app(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Application::class, 'app_id');
    }
    // to user
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
