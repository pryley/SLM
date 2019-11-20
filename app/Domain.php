<?php

namespace App;

class Domain extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'license_id',
        'domain',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'license_id',
    ];

    /**
     * @var array
     */
    public $rules = [
        'domain' => 'required',
    ];

    /**
     * Get the license that owns the domain.
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
