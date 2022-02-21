<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'user_id', 'service_id', 'value', 'scheduled_to'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $casts = [
        'value' => 'float'
    ];

    public function service(){
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'scheduled_to' => $this->scheduled_to,
            'user_confirmation' => $this->user_confirmation,
            'worker_confirmation' => $this->worker_confirmation,
            'value' => floatval($this->value),
            'note' => $this->note,
            'service_id' => $this->service_id,
            'service' => $this->service,
            'user_id' => $this->user_id,
            'customer' => $this->user,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }


}
