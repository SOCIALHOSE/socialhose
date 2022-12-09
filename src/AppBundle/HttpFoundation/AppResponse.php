<?php

namespace AppBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SearchResponse
 * Response from application api.
 *
 * @package AuthenticationBundle\HttpFoundation
 */
class AppResponse extends JsonResponse
{

    /**
     * SearchResponse constructor.
     *
     * @param mixed   $data    The response data.
     * @param integer $status  The response status code.
     * @param array   $headers An array of response headers.
     */
    public function __construct($data = null, $status = 200, array $headers = [])
    {
        // Initialize current response data first.
        $this->data = [];

        if ($data === null) {
            $data = [];
        } elseif (is_scalar($data)) {
            $data = [ $data ];
        }
        $headers['Content-Type'] = 'application/json';

        parent::__construct($data, $status, $headers);
        $this->update();
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $data Response data.
     *
     * @return AppResponse
     */
    public function setData($data = [])
    {
        // Do not serialize data into json here, but do it in update method.
        if ($data === null) {
            $data = [];
        } elseif (is_scalar($data)) {
            $data = [ $data ];
        }
        $this->data = $data;
        $this->update();

        return $this;
    }

    /**
     * Updates the content and headers according to the JSON data and callback.
     *
     * @return AppResponse
     */
    protected function update()
    {
        $data = $this->data;

        if (($this->statusCode >= 400) && ($this->statusCode !== 402)) {
            $data = [
                'errors' => $data,
            ];
        }

        return $this->setContent(json_encode($data));
    }

    /**
     * Create response with 400 HTTP code.
     *
     * @param mixed $data The response data.
     *
     * @return static
     */
    public static function badRequest($data = 'Bad Request')
    {
        return static::create($data, self::HTTP_BAD_REQUEST);
    }

    /**
     * Create response with 401 HTTP code.
     *
     * @param string $data The response data.
     *
     * @return static
     */
    public static function unauthorized($data = 'Unauthorized')
    {
        return static::create($data, self::HTTP_UNAUTHORIZED);
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return AppResponse
     */
    public function send()
    {
        $this->update();
        parent::send();

        return $this;
    }
}
