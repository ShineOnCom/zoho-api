<?php

namespace ShineOnCom\Zoho;

use BadMethodCallException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use ShineOnCom\Zoho\Exceptions\InvalidOrMissingEndpointException;
use ShineOnCom\Zoho\Exceptions\ModelNotFoundException;
use ShineOnCom\Zoho\Helpers\Util;
use ShineOnCom\Zoho\Models\AbstractModel;
use ShineOnCom\Zoho\Models\Contact;

/**
 * Class Zoho
 *
 * @property \ShineOnCom\Zoho\Api\Contacts $contacts
 * @method \ShineOnCom\Zoho\Api\Contacts contacts(string $contact_id)
 */
class Zoho extends Client
{
    /**
     * The current endpoint for the API
     *
     * @var string $api
     */
    public $api;

    /** @var array $ids */
    public $ids = [];

    /**
     * Methods / Params queued for API call
     *
     * @var array $queue
     */
    public $queue = [];

    /** @var string $base */
    public static $base = 'crm/v2';

    /**
     * Our list of valid Zoho endpoints.
     *
     * @var array $endpoints
     */
    protected static $endpoints = [
        'contacts' => 'Contacts/%s',
    ];

    /** @var array $resource_helpers */
    protected static $resource_models = [
        'contacts' => Contact::class,
    ];

    /**
     * Zoho constructor.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        parent::__construct([
            'base_uri' => static::$base,
            'headers'  => [
                'Authorization' => "Zoho-oauthtoken {$token}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json; charset=utf-8;'
            ]
        ]);
    }

    /**
     * @param string $token
     * @return Zoho
     */
    public static function make($token)
    {
        return new static($token);
    }

    /**
     * Get a resource using the assigned endpoint ($this->endpoint).
     *
     * @param array $query
     * @param string $append
     * @return array
     * @throws InvalidOrMissingEndpointException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($query = [], $append = '')
    {
        $api = $this->api;

        $response = $this->request(
            $method = 'GET',
            $uri = $this->uri($append),
            $options = ['query' => $query]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data[static::apiCollectionProperty($api)])) {
            return $data[static::apiCollectionProperty($api)];
        }

        if (isset($data[static::apiEntityProperty($api)])) {
            return $data[static::apiEntityProperty($api)];
        }

        return $data;
    }

    /**
     * Post to a resource using the assigned endpoint ($this->api).
     *
     * @param array|AbstractModel $payload
     * @param string $append
     * @return array|AbstractModel
     * @throws InvalidOrMissingEndpointException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($payload = [], $append = '')
    {
        return $this->post_or_put('POST', $payload, $append);
    }

    /**
     * Update a resource using the assigned endpoint ($this->api).
     *
     * @param array|AbstractModel $payload
     * @param string $append
     * @return array|AbstractModel
     * @throws InvalidOrMissingEndpointException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($payload = [], $append = '')
    {
        return $this->post_or_put('PUT', $payload, $append);
    }

    /**
     * @param $post_or_post
     * @param array $payload
     * @param string $append
     * @return mixed
     * @throws InvalidOrMissingEndpointException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function post_or_put($post_or_post, $payload = [], $append = '')
    {
        $payload = $this->normalizePayload($payload);
        $api = $this->api;
        $uri = $this->uri($append);

        $json = $payload instanceof AbstractModel
            ? $payload->getPayload()
            : $payload;

        $response = $this->request(
            $method = $post_or_post,
            $uri,
            $options = compact('json')
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data[static::apiEntityProperty($api)])) {
            $data = $data[static::apiEntityProperty($api)];

            if ($payload instanceof AbstractModel) {
                $payload->syncOriginal($data);

                return $payload;
            }
        }

        return $data;
    }

    /**
     * Delete a resource using the assigned endpoint ($this->api).
     *
     * @param array|string $query
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ShineOnCom\Zoho\Exceptions\InvalidOrMissingEndpointException
     */
    public function delete($query = [])
    {
        $response = $this->request(
            $method = 'DELETE',
            $uri = $this->uri(),
            $options = ['query' => $query]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $id
     * @return AbstractModel|null
     * @throws ModelNotFoundException|InvalidOrMissingEndpointException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function find($id)
    {
        try {
            $data = $this->get([], $args = $id);

            return $this->getModel($data);
        } catch (ClientException $ce) {
            if ($ce->getResponse()->getStatusCode() == 404) {
                $msg = sprintf('Model(%s) not found for `%s`',
                    $id, $this->api);

                throw new ModelNotFoundException($msg);
            }

            throw $ce;
        }
    }

    /**
     * Use the Query API
     * https://www.zoho.com/crm/help/developer/api/COQL-Overview.html
     *
     * @param string $select_query
     * @param bool $pluck_data
     * @return AbstractModel|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($select_query, $pluck_data = true)
    {
        $response = $this->request(
            'POST',
            static::$base . '/coql',
            [
                'json' => compact('select_query')
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Return an array of models or Collection (if Laravel present)
     *
     * @param string|array $ids
     * @return array|\Illuminate\Support\Collection
     * @throws InvalidOrMissingEndpointException
     */
    public function findMany($ids)
    {
        if (is_array($ids)) {
            $ids = implode(',', array_filter($ids));
        }

        return $this->all(compact('ids'));
    }

    /**
     * Zoho limits to 250 results
     *
     * @param array $query
     * @param string $append
     * @return array|\Illuminate\Support\Collection
     * @throws InvalidOrMissingEndpointException
     */
    public function all($query = [], $append = '')
    {
        $data = $this->get($query, $append);

        if (static::$resource_models[$this->api]) {
            $class = static::$resource_models[$this->api];

            if (isset($data[$class::$resource_name_many])) {
                $data = $data[$class::$resource_name_many];
            }

            $data = array_map(function($arr) use ($class) {
                return new $class($arr);
            }, $data);

            return defined('LARAVEL_START') ? collect($data) : $data;
        }

        return $data;
    }

    /**
     * Post to a resource using the assigned endpoint ($this->api).
     *
     * @param AbstractModel $model
     * @return AbstractModel
     * @throws InvalidOrMissingEndpointException|\GuzzleHttp\Exception\GuzzleException
     */
    public function save(AbstractModel $model)
    {
        // Filtered by uri() if falsy
        $id = $model->getAttribute($model::$identifier);

        $this->api = $model::$resource_name_many;

        $response = $this->request(
            $method = $id ? 'PUT' : 'POST',
            $uri = $this->uri(),
            $options = ['json' => $model->getPayload()]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data[$model::$resource_name])) {
            $data = $data[$model::$resource_name];
        }

        $model->syncOriginal($data);

        return $model;
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function destroy(AbstractModel $model)
    {
        $response = $this->delete($model->getOriginal($model::$identifier));

        if ($success = is_array($response) && empty($response)) {
            $model->exists = false;
        }

        return $success;
    }

    /**
     * @param string $append
     * @return string
     * @throws InvalidOrMissingEndpointException
     */
    public function uri($append = '')
    {
        $uri = static::makeUri($this->api, $this->ids, $this->queue, $append);

        $this->ids = [];
        $this->queue = [];

        return $uri;
    }

    /**
     * @param string $api
     * @param array $ids
     * @param array $queue
     * @param string $append
     * @return string
     * @throws InvalidOrMissingEndpointException
     */
    private static function makeUri($api, $ids = [], $queue = [], $append = '')
    {
        // Is it an entity endpoint?
        if (substr_count(static::$endpoints[$api], '%') == count($ids)) {
            $endpoint = vsprintf(static::$endpoints[$api], $ids);

        // Is it a collection endpoint?
        } elseif (substr_count(static::$endpoints[$api], '%') == (count($ids) + 1)) {
            $endpoint = vsprintf(str_replace('/%s', '', static::$endpoints[$api]), $ids);

        // Is it just plain wrong?
        } else {
            $msg = sprintf('You did not specify enough ids for endpoint `%s`, ids(%s).',
                static::$endpoints[$api],
                implode($ids));

            throw new InvalidOrMissingEndpointException($msg);
        }

        // Prepend parent APIs until none left.
        while ($parent = array_shift($queue)) {
            $endpoint = implode('/', array_filter($parent)).'/'.$endpoint;
        }

        $endpoint = '/'.static::$base.'/'.$endpoint;

        if ($append) {
            $endpoint .= '/'.$append;
        }

        return $endpoint;
    }

    /**
     * @param $payload
     * @return mixed
     */
    private function normalizePayload($payload)
    {
        if ($payload instanceof AbstractModel) {
            return $payload;
        }

        if (! isset($payload['id'])) {
            if ($count = count($args = array_filter($this->ids))) {
                $last = $args[$count-1];
                if (is_numeric($last)) {
                    $payload['id'] = $last;
                }
            }
        }

        $entity = $this->getApiEntityProperty();

        return [$entity => $payload];
    }

    /**
     * @return string
     */
    private function getApiCollectionProperty()
    {
        return static::apiCollectionProperty($this->api);
    }

    /**
     * @param string $api
     * @return string
     */
    private static function apiCollectionProperty($api)
    {
        /** @var AbstractModel $model */
        $model = static::$resource_models[$api];
        return $model::$resource_name_many;
    }

    /**
     * @return string
     */
    private function getApiEntityProperty()
    {
        return static::apiEntityProperty($this->api);
    }

    /**
     * @param string $api
     * @return string
     */
    private function apiEntityProperty($api)
    {
        /** @var AbstractModel $model */
        $model = static::$resource_models[$api];
        return $model::$resource_name;
    }

    /**
     * Set our endpoint by accessing it like a property.
     *
     * @param string $endpoint
     * @return $this
     */
    public function __get($endpoint)
    {
        if (array_key_exists($endpoint, static::$endpoints)) {
            $this->api = $endpoint;
        }

        $className = "ShineOnCom\Zoho\\Api\\" . Util::studly($endpoint);

        if (class_exists($className)) {
            return new $className($this);
        }

        return $this;
    }

    /**
     * Set ids for one uri() call.
     *
     * @param string $method
     * @param array $parameters
     * @return $this
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (array_key_exists($method, static::$endpoints)) {
            $this->ids = array_merge($this->ids, $parameters);
            return $this->__get($method);
        }
        $msg = sprintf('Method %s does not exist.', $method);

        throw new BadMethodCallException($msg);
    }

    /**
     * @param $responseStack
     * @return Helpers\Testing\ZohoMock
     */
    public static function fake($responseStack = [])
    {
        return new Helpers\Testing\ZohoMock($responseStack);
    }

    /**
     * Wrapper to the $client->request method
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri = '', array $options = [])
    {
//        if (env('ZOHO_OPTION_LOG_API_REQUEST') || (function_exists('config')) && config('zoho.options.log_api_request_data')) {
//            Log::info('Zoho API Request', compact('method', 'uri') + $options);
//        }
        return parent::request($method, $uri, $options);
    }

    /**
     * @param $data
     * @return AbstractModel
     */
    public function getModel($data)
    {
        $class = static::$resource_models[$this->api];

        return new $class($data['data'][0]);
    }
}
