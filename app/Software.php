<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Software extends Model
{
	use SoftDeletes;

	protected $table = 'software';

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = [
		'deleted_at',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'repository',
		'product_id',
		'status',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'id',
	];

	/**
	 * @var array
	 */
	public $rules = [
		'name' => 'required',
		'repository' => 'url',
		'product_id' => 'required|alpha_dash|unique:software',
		'status' => 'in:active,archived',
	];

	/**
	 * Determine if the software has a license set.
	 *
	 * @return bool
	 */
    public function hasLicense($license)
	{
        return $this->licenses()->where('license_key', $license)->first();
	}

	/**
	 * Get all of the licenses for the software.
	 */
	public function licenses()
	{
        return $this->belongsToMany(License::class, 'software_licenses', 'software_id', 'license_id');
	}

	/**
	 * Get all of the updates for the software.
	 */
	public function updates()
	{
        return $this->hasMany(Update::class, 'software_id');
	}
}
