<?php namespace JobApis\Jobs\Client\Providers;

use JobApis\Jobs\Client\Job;

class IndeedClient extends AbstractProvider
{
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
     * Get country
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getCountry()
    {
        return $this->queryParams['co'];
    }

    /**
     * Get days back to search
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getDaysBack()
    {
        return $this->queryParams['fromage'];
    }

    /**
     * Get filter duplicates
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getFilterDuplicates()
    {
        return $this->queryParams['filter'];
    }

    /**
     * Get include latitude and longitude
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getIncludeLatLong()
    {
        return $this->queryParams['latlong'];
    }

    /**
     * Get job type
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getJobType()
    {
        return $this->queryParams['jt'];
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
     * Get location for search query
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getLocation()
    {
        return $this->queryParams['l'];
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
     * Get Site type
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getSiteType()
    {
        return $this->queryParams['st'];
    }

    /**
     * Get User IP
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getUserIp()
    {
        return $this->queryParams['userip'];
    }

    /**
     * Get User Agent
     *
     * @return string Should return the value of the parameter describing this query
     */
    public function getUserAgent()
    {
        return $this->queryParams['useragent'];
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
     * Set Country for query
     *
     * @return Indeed
     */
    public function setCountry($value)
    {
        $this->co = $value;
        return $this;
    }

    /**
     * Set days back for query
     *
     * @return Indeed
     */
    public function setDaysBack($value)
    {
        $this->fromage = $value;
        return $this;
    }

    /**
     * Updates query params to include integer representation of boolean value
     * to filter results for duplicates or not.
     *
     * @param  mixed  $value
     *
     * @return Indeed
     */
    public function setFilterDuplicates($value)
    {
        $this->filter = (bool) $value ? '1' : null;
        return $this;
    }

    /**
     * Updates query params to include integer representation of boolean value
     * to include lattitude and longitude in results.
     *
     * @param  mixed  $value
     *
     * @return Indeed
     */
    public function setIncludeLatLong($value)
    {
        $this->latlong = (bool) $value ? '1' : null;
        return $this;
    }

    /**
     * Set job type for query
     *
     * @return Indeed
     */
    public function setJobType($value)
    {
        $this->jt = $value;
        return $this;
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
     * Set location for search query
     *
     * @return Indeed
     */
    public function setLocation($value)
    {
        $this->l = $value;
        return $this;
    }

    /**
     * Set site type for query
     *
     * @return Indeed
     */
    public function setSiteType($value)
    {
        $this->st = $value;
        return $this;
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
