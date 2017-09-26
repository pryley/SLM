<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidSoftwareException;
use App\Software;
use App\Transformers\SoftwareTransformer;
use Illuminate\Http\Request;

class SoftwareController extends Controller
{
	public function __construct( SoftwareTransformer $transformer )
	{
		$this->transformer = $transformer;
		parent::__construct();
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws InvalidSoftwareException
	 */
	public function archive( Request $request )
	{
		if( $software = app( License::class )->where( 'slug', $request->input( 'slug' ))->first() ) {
			$software->status = 'archived';
			$software->save();
			$software->delete();
			$software->fireEvent( 'archived' );
			return $this->respondWithItem( $software, $this->transformer );
		}
		throw new InvalidSoftwareException;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
	{
		return $this->respondWithCollection( app( Software::class )->all(), $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store( Request $request )
	{
		$software = app( Software::class );
		$this->validate( $request, $software->rules );
		$software->fill([
			'name' => $request->input( 'name' ),
			'slug' => $request->input( 'slug' ),
			'status' => 'active',
		])->save();
		return $this->respondWithItem( $software, $this->transformer );
	}

	/**
	 * @param string $softwareId
	 * @return \Illuminate\Http\JsonResponse
	 * @throws InvalidSoftwareException
	 */
	public function destroy( $softwareSlug )
	{
		if( $software = app( Software::class )->withTrashed()->where( 'slug', $softwareSlug )->first() ) {
			$software->forceDelete();
			$software->fireEvent( 'removed' );
			return $this->sendCustomResponse( 204, 'Software deleted' );
		}
		throw new InvalidSoftwareException;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws InvalidSoftwareException
	 */
	public function restore( Request $request )
	{
		$software = app( License::class )
			->onlyTrashed()
			->where( 'slug', $request->input( 'slug' ))
			->first();
		if( $software ) {
			$software->status = 'active';
			$software->save();
			$software->restore();
			return $this->respondWithItem( $software, $this->transformer );
		}
		throw new InvalidSoftwareException;
	}
}
