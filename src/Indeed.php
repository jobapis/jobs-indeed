<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Indeed extends AbstractProvider
{
    /**
     * Base API Url
     *
     * @var string
     */
    protected $baseUrl = 'http://api.indeed.com/ads/apisearch?';

    /**
     * Job defaults
     *
     * @var array
     */
    protected $jobDefaults = ['jobtitle','company','formattedLocation','source',
        'date','snippet','url','jobkey'
    ];

    /**
     * Map of setter methods to query parameters
     *
     * @var array
     */
    protected $queryMap = [
        'setVersion' => 'v',
        'setKeyword' => 'q',
        'setLocation' => 'l',
        'setSiteType' => 'st',
        'setJobType' => 'jt',
        'setPage' => 'start',
        'setCount' => 'limit',
        'setDaysBack' => 'fromage',
        'filterDuplicates' => 'filter',
        'includeLatLong' => 'latlong',
        'setCountry' => 'co',
        'setChannel' => 'chnl',
        'setUserIp' => 'userip',
        'setUserAgent' => 'useragent',
    ];

    /**
     * Query params
     *
     * @var array
     */
    protected $queryParams = [
        'publisher' => null,
        'v' => '2',
        'format' => null,
        'q' => null,
        'l' => null,
        'sort' => null,
        'radius' => null,
        'st' => null,
        'jt' => null,
        'start' => null,
        'limit' => null,
        'fromage' => null,
        'highlight' => null,
        'filter' => null,
        'latlong' => null,
        'co' => null,
        'chnl' => null,
        'userip' => null,
        'useragent' => null,
    ];

    /**
     * Required params
     *
     * @var array
     */
    protected $requiredParams = [
        'publisher',
    ];

    /**
     * Create new indeed jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->addDefaultUserInformationToParameters($parameters);
        $this->addDefaultFormatToParameters($parameters);
        parent::__construct($parameters);
    }

    /**
     * Defaults to json if no format is initially provided
     *
     * @param array  $parameters
     *
     * @return void
     */
    protected function addDefaultFormatToParameters(&$parameters = [])
    {
        if (!isset($parameters['format'])) {
            $parameters['format'] = $this->getFormat();
        }
    }

    /**
     * Attempts to apply default user information to parameters when none provided.
     *
     * @param array  $parameters
     *
     * @return void
     */
    protected function addDefaultUserInformationToParameters(&$parameters = [])
    {
        $defaultKeys = [
            'userip' => 'REMOTE_ADDR',
            'useragent' => 'HTTP_USER_AGENT',
        ];

        array_walk($defaultKeys, function ($value, $key) use (&$parameters) {
            if (!isset($parameters[$key]) && isset($_SERVER[$value])) {
                $parameters[$key] = $_SERVER[$value];
            }
        });
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
        $payload = static::parseAttributeDefaults($payload, $this->jobDefaults);

        $job = $this->createJobFromPayload($payload);

        $job = $this->setJobLocation($job, $payload['formattedLocation']);

        return $job->setCompany($payload['company'])
            ->setDatePostedAsString($payload['date']);
    }

    /**
     * Create new job from given payload
     *
     * @param  array $payload
     *
     * @return Job
     */
    protected function createJobFromPayload($payload = [])
    {
        return new Job([
            'title' => $payload['jobtitle'],
            'name' => $payload['jobtitle'],
            'description' => $payload['snippet'],
            'url' => $payload['url'],
            'sourceId' => $payload['jobkey'],
            'location' => $payload['formattedLocation'],
        ]);
    }

    /**
     * Updates query params to include integer representation of boolean value
     * to filter results for duplicates or not.
     *
     * @param  mixed  $value
     *
     * @return Indeed
     */
    public function filterDuplicates($value)
    {
        $filter = (bool) $value ? '1' : null;

        return $this->updateQuery($filter, 'filter');
    }

    /**
     * Get data format
     *
     * @return string
     */
    public function getFormat()
    {
        $validFormats = ['json', 'xml'];

        if (isset($this->queryParams['format'])
            && in_array(strtolower($this->queryParams['format']), $validFormats)) {
            return strtolower($this->queryParams['format']);
        }

        return 'json';
    }

    /**
     * Get keyword for search query
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getKeyword()
    {
        return 'q';
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
     * Get http verb
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }

    /**
     * Updates query params to include integer representation of boolean value
     * to include lattitude and longitud in results.
     *
     * @param  mixed  $value
     *
     * @return Indeed
     */
    public function includeLatLong($value)
    {
        $latlong = (bool) $value ? '1' : null;

        return $this->updateQuery($latlong, 'latlong');
    }

    /**
     * Attempt to parse and add location to Job
     *
     * @param Job     $job
     * @param string  $location
     *
     * @return  Job
     */
    private function setJobLocation(Job $job, $location)
    {
        $location = static::parseLocation($location);

        if (isset($location[0])) {
            $job->setCity($location[0]);
        }
        if (isset($location[1])) {
            $job->setState($location[1]);
        }

        return $job;
    }
}
