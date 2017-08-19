<?php

namespace App\Exceptions;

use App\Exceptions\InvalidDomainException;
use App\Exceptions\InvalidLicenseException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		AuthorizationException::class,
		HttpException::class,
		ModelNotFoundException::class,
		ValidationException::class,
		// InvalidDomainException::class,
		// InvalidLicenseException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @return void
	 */
	public function report( Exception $e )
	{
		parent::report( $e );
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function render( $request, Exception $e )
	{
		if( $e instanceof AuthorizationException ) {
			return response()->json(([
				'status' => 403,
				'message' => 'Insufficient privileges to perform this action',
			]), 403 );
		}
		if( $e instanceof InvalidDomainException ) {
			return response()->json(([
				'status' => 404,
				'message' => 'The domain is invalid',
			]), 404 );
		}
		if( $e instanceof InvalidLicenseException ) {
			return response()->json(([
				'status' => 404,
				'message' => 'The license is invalid',
			]), 404 );
		}
		if( $e instanceof MethodNotAllowedHttpException ) {
			return response()->json(([
				'status' => 405,
				'message' => 'Method Not Allowed',
			]), 405 );
		}
		if( $e instanceof NotFoundHttpException ) {
			return response()->json(([
				'status' => 404,
				'message' => 'The requested resource was not found',
			]), 404 );
		}
		return parent::render( $request, $e );
	}
}
