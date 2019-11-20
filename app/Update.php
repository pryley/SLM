<?php

namespace App;

class Update extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'version',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'software_id',
    ];

    /**
     * @var array
     */
    public $rules = [
        'version' => 'required|regex:/^v\d{1,3}\.\d{1,3}\.\d{1,3}$/',
    ];

    /**
     * Get the software that owns the update.
     */
    public function software()
    {
        return $this->belongsTo(Software::class, 'software_id');
    }
}
