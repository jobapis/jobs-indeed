<?php namespace JobApis\Jobs\Client\Providers;

use JobApis\Jobs\Client\Job;

class Indeed extends AbstractProvider
{
    /**
     * Base API Url
     *
     * @var string
     */
    protected $baseUrl = 'http://api.indeed.com/ads/apisearch';

    /**
     * Required params
     *
     * @var array
     */
    protected $requiredParams = [
        'publisher',
    ];

    /**
     * Get default parameters and values
     *
     * @return  string
     */
    public function defaultParameters()
    {
        return [
            'publisher' => null,
            'v' => '2',
            'format' => 'json',
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
            'userip' => $this->userIp(),
            'useragent' => $this->userAgent(),
        ];
    }

    /**
     * Job object default keys that must be set.
     *
     * @return  string
     */
    public function defaultResponseFields()
    {
        return [
            'jobtitle',
            'company',
            'formattedLocation',
            'formattedLocationFull',
            'source',
            'date',
            'snippet',
            'url',
            'jobkey',
            'latitude',
            'longitude'
        ];
    }

    /**
     * Get parameters that MUST be set in order to satisfy the APIs requirements
     *
     * @return  string
     */
    public function requiredParameters()
    {
        return [
            'publisher'
        ];
    }

    /**
     * Get parameters that CAN be set
     *
     * @return  string
     */
    public function validParameters()
    {
        return [
            'publisher',
            'v',
            'format',
            'q',
            'l',
            'sort',
            'radius',
            'st',
            'jt',
            'start',
            'limit',
            'fromage',
            'highlight',
            'filter',
            'latlong',
            'co',
            'chnl',
            'userip',
            'useragent',
        ];
    }

    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobApis\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $job = $this->createJobFromPayload($payload);

        $job = $this->setJobLocation($job, $payload['formattedLocation']);

        $postalCode = str_replace($payload['formattedLocation'].' ', '', $payload['formattedLocationFull']);

        return $job->setCompany($payload['company'])
            ->setDatePostedAsString($payload['date'])
            ->setPostalCode($postalCode)
            ->setLatitude($payload['latitude'])
            ->setLongitude($payload['longitude']);
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
     * Get keyword for search query
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getKeyword()
    {
        return $this->queryParams['q'];
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
     * Updates query params to include integer representation of boolean value
     * to include lattitude and longitude in results.
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
     * Set keyword for search query
     *
     * @return Indeed
     */
    public function setKeyword($value)
    {
        $this->q = $value;
        return $this;
    }

    /**
     * Return the user's IP address
     *
     * @return  string
     */
    public function userAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Return the user's IP address
     *
     * @return  string
     */
    public function userIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
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
