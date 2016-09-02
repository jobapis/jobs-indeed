<?php namespace JobApis\Jobs\Client\Queries;

class IndeedQuery extends AbstractQuery
{
    /**
     * API Version. Should be 2.
     *
     * @var integer
     */
    const API_VERSION = 2;

    /**
     * Response format.
     *
     * @var integer
     */
    const API_FORMAT = 'json';

    /**
     * User Agent
     *
     * @var string
     */
    protected $useragent;

    /**
     * Client IP Address
     *
     * @var string
     */
    protected $userip;

    /**
     * Channel group
     *
     * @var string
     */
    protected $chnl;

    /**
     * Country
     *
     * @var string
     */
    protected $co;

    /**
     * Include latitude and longitude in results
     *
     * @var boolean
     */
    protected $latlong;

    /**
     * Filter duplicate results
     *
     * @var boolean
     */
    protected $filter;

    /**
     * Highlight results
     *
     * @var boolean
     */
    protected $highlight;

    /**
     * Days back to search
     *
     * @var string
     */
    protected $fromage;

    /**
     * Max number of results
     *
     * @var string
     */
    protected $limit;

    /**
     * Start results from this index
     *
     * @var string
     */
    protected $start;

    /**
     * Job type
     *
     * @var string
     */
    protected $jt;

    /**
     * Site type
     *
     * @var string
     */
    protected $st;

    /**
     * Radius around location to search
     *
     * @var string
     */
    protected $radius;

    /**
     * Sort by relevance or date
     *
     * @var string
     */
    protected $sort;

    /**
     * Location
     *
     * @var string
     */
    protected $l;

    /**
     * Javascript function for callback
     *
     * @var string
     */
    protected $callback;

    /**
     * JSON or XML format
     *
     * @var string
     */
    protected $format;

    /**
     * Version number
     *
     * @var string
     */
    protected $v;

    /**
     * Publisher ID
     *
     * @var string
     */
    protected $publisher;

    /**
     * Query
     *
     * @var string
     */
    protected $q;

    /**
     * Get baseUrl
     *
     * @return  string Value of the base url to this api
     */
    public function getBaseUrl()
    {
        return 'http://api.indeed.com/ads/apisearch';
    }

    /**
     * Get keyword
     *
     * @return  string Attribute being used as the search keyword
     */
    public function getKeyword()
    {
        return $this->q;
    }

    /**
     * Default parameters
     *
     * @return array
     */
    protected function defaultAttributes()
    {
        return [
            'useragent' => $this->userAgent(),
            'userip' => $this->userIp(),
            'v' => static::API_VERSION,
            'format' => static::API_FORMAT,
        ];
    }

    /**
     * Required parameters
     *
     * @return array
     */
    protected function requiredAttributes()
    {
        return [
            'useragent',
            'userip',
            'publisher',
        ];
    }

    /**
     * Return the user agent from server
     *
     * @return  string
     */
    protected function userAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Return the IP address from server
     *
     * @return  string
     */
    protected function userIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }
}
