<?php namespace JobApis\Jobs\Client\Providers;

use JobApis\Jobs\Client\Job;

class IndeedProvider extends AbstractProvider
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
        $job = new Job([
            'title' => $payload['jobtitle'],
            'name' => $payload['jobtitle'],
            'description' => $payload['snippet'],
            'url' => $payload['url'],
            'sourceId' => $payload['jobkey'],
            'location' => $payload['formattedLocation'],
        ]);

        $job = $this->setJobLocation($job, $payload['formattedLocation']);

        $postalCode = str_replace($payload['formattedLocation'].' ', '', $payload['formattedLocationFull']);

        return $job->setCompany($payload['company'])
            ->setDatePostedAsString($payload['date'])
            ->setPostalCode($postalCode)
            ->setLatitude($payload['latitude'])
            ->setLongitude($payload['longitude']);
    }

    /**
     * Job response object default keys that should be set
     *
     * @return  string
     */
    public function getDefaultResponseFields()
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
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'results';
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
