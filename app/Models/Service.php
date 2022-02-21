<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'value', 'worker_id'
    ];

    protected $casts = [
        'value' => 'float'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function worker(){
        return $this->belongsTo(Worker::class, 'worker_id', 'id');
    }

    public function contracts(){
        return $this->hasMany(Contract::class, 'service_id', 'id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'value' => $this->value,
            'worker' => $this->worker->user,
            'worker_id' => $this->worker_id
        ];
    }

}
