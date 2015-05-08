<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Indeed extends AbstractProvider
{
    /**
     * Publisher Id
     *
     * @var string
     */
    protected $publisherId;

    /**
     * Version
     *
     * @var string
     */
    protected $version;

    /**
     * Highlight
     *
     * @var string
     */
    protected $highlight;

    /**
     * Query params
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * Add query params, if valid
     *
     * @param string $value
     * @param string $key
     *
     * @return  void
     */
    private function addToQueryStringIfValid($value, $key)
    {
        $computed_value = $this->$value();
        if (!is_null($computed_value)) {
            $this->queryParams[$key] = $computed_value;
        }
    }

    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $defaults = ['jobtitle', 'company', 'formattedLocation', 'source',
            'date', 'snippet', 'url', 'jobkey'];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['jobtitle'],
            'description' => $payload['snippet'],
            'url' => $payload['url'],
            'sourceId' => $payload['jobkey'],
            'company' => $payload['company'],
            'location' => $payload['formattedLocation'],
        ]);

        return $job;
    }

    /**
     * Get data format
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'results';
    }

    /**
     * Get combined location
     *
     * @return string
     */
    public function getLocation()
    {
        $location = ($this->city ? $this->city.', ' : null).($this->state ?: null);

        if ($location) {
            return $location;
        }

        return null;
    }

    /**
     * Get parameters
     *
     * @return  array
     */
    public function getParameters()
    {
        return [];
    }

    /**
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
        $query_params = [
            'publisher' => 'getPublisherId',
            'v' => 'getVersion',
            'highlight' => 'getHighlight',
            'format' => 'getFormat',
            'q' => 'getKeyword',
            'l' => 'getLocation',
            'start' => 'getPage',
            'limit' => 'getCount',
        ];

        array_walk($query_params, [$this, 'addToQueryStringIfValid']);

        return http_build_query($this->queryParams);
    }

    /**
     * Get url
     *
     * @return  string
     */
    public function getUrl()
    {
        $query_string = $this->getQueryString();

        return 'http://api.indeed.com/ads/apisearch?'.$query_string;
    }

    /**
     * Get http verb
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }
}
