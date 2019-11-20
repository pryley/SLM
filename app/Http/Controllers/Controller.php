<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidLicenseException;
use App\License;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class Controller extends BaseController
{
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * Status code of response.
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * Constructor.
     *
     * @param Manager|null $fractal
     */
    public function __construct()
    {
        $this->fractal = new Manager();
    }

    /**
     * @param string $licenseKey
     * @param bool $isTrashed
     * @return License
     * @throws InvalidLicenseException
     */
    public function getLicense($licenseKey, $isTrashed = false)
    {
        $model = $isTrashed
            ? app(License::class)->onlyTrashed()
            : app(License::class);
        if ($license = $model->where('license_key', $licenseKey)->first()) {
            return $license;
        }
        throw new InvalidLicenseException();
    }

    /**
     * @param int $status
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCustomResponse($status, $message)
    {
        return response()->json(['status' => $status, 'message' => $message], $status);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendForbiddenResponse()
    {
        return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
    }

    /**
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvalidFieldResponse($errors)
    {
        return response()->json(['status' => 400, 'invalid_fields' => $errors], 400);
    }

    /**
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvalidFilterResponse($errors)
    {
        return response()->json(['status' => 400, 'invalid_filters' => $errors], 400);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotFoundResponse($message = '')
    {
        if (empty($message)) {
            $message = 'The requested resource was not found';
        }
        return response()->json(['status' => 404, 'message' => $message], 404);
    }

    /**
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendUnknownFieldResponse($errors)
    {
        return response()->json(['status' => 400, 'unknown_fields' => $errors], 400);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        if (empty($array['status'])) {
            $array['status'] = $this->statusCode;
        }
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * @param array|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection $collection
     * @param \Closure|TransformerAbstract $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection, $callback)
    {
        if (!empty($collection)) {
            $resource = new Collection($collection, $callback);
        } else {
            $collection = new LengthAwarePaginator([], 0, 10);
            $resource = new Collection($collection, $callback);
            $resource->setPaginator(new IlluminatePaginatorAdapter($collection));
        }
        return $this->respondWithArray($this->fractal->createData($resource)->toArray());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $item
     * @param \Closure|TransformerAbstract $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithItem($item, $callback)
    {
        $resource = new Item($item, $callback);
        return $this->respondWithArray($this->fractal->createData($resource)->toArray());
    }
}
