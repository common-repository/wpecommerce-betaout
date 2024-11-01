<?php

/**
 * GetAmplify.com is a marketing automation software and enagegment platform
 *
 * This library provides connectivity with the Amplify API
 *
 * Basic usage:
 *
 * 1. Configure Amplify with your access credentials
 * <code>
 * <?php
 *
 * $amplify = new Amplify('dummy_api_key','dummy_api_secret','dummy_project_id');
 * ?>
 * </code>
 *
 * 2. Make requests to the API
 * <code>
 * <?php
 * $amplify = new Amplify('dummy_app_key','dummy_app_secret','dummy_project_id');
 * amplify->identify('sandeep@socialaxishq.com','Sandeep');
 *
 * ?>
 * </code>
 *
 * @author Sandeep Kaushal Verma <sandeep@socialaxishq.com>
 * @copyright Copyright 2013 Betaout Pvt Ltd All rights reserved.
 * @link http://www.betaout.com/
 * @license http://opensource.org/licenses/MIT
 * */

if (!class_exists('Amplify')) {
class Amplify {
    
    /*
     * the amplify ApiKey
     */

    protected $showError = array();
    protected $apiKey;

    /*
     * the amplify ApiSecret
     */
    protected $apiSecret;
    public $hitcount = 0;

    /*
     * the amplify ProjectId
     */
    protected $projectId;

    /*
     * the amplify requesturl
     *
     */
    protected $requestUrl;
    /*
     * the amplify custom URL
     *
     */
    protected $publicationUrl;

    /**
     * amplify host
     */
    private $host = 'api.betaout.com';

    /**
     * amplify version
     */
    private $version = 'v2';

    /*
     * param to be send on amplify
     */
    protected $params;

    /*
     * Computes a Hash-based Message Authentication Code (HMAC) using the SHA1 hash function.
     */
    protected $signatureMethod = 'HMAC-SHA1';

    /*
     * current time stamp used to create hash
     */
    protected $timeStamp;
    /*
     * ott refer one time token that use to handshake
     */
    protected $ott;

    /**
     * Whether we are in debug mode. This is set by the constructor
     */
    private $debug = true;

    /**
     * gettting device info
     */
    private $deviceDetect = 1;

    /**
     * function end point mapping
     */
    protected $functionUrlMap = array(
                'identify' => 'user/identify/',
                'event' => 'user/events/',
                'customer_action' => 'ecommerce/activities',
            );
    /**
     * The constructor
     *
     * @param string $apiKey The Amplify application Key
     * @param string $apiSecret The Amplify application Secret
     * @param string $projectId The Amplify ProjectId
     * @param string $debug Optional debug flag
     * @return void
     * */
    public function __construct($amplifyApiKey = "", $amplifyProjectId = "", $debug = false) {
        $apiKey = !empty($amplifyApiKey) ? $amplifyApiKey : get_option('_AMPLIFY_API_KEY');
        $projectId = !empty($amplifyProjectId) ? $amplifyProjectId : get_option('_AMPLIFY_PROJECT_ID');
        $this->setApiKey($apiKey);
        $this->setProjectId($projectId);
        $this->setPublicationUrl();
        $this->setTimeStamp(time());
        // $this->setOtt();
        $this->debug = $debug;
    }

    private function basicSetUp() {
        if (function_exists('curl_init')) {
            $this->showError[] = 'Amplify PHP SDK requires the CURL PHP extension';
        }

        if (!function_exists('json_decode')) {
            $this->showError[] = 'Amplify PHP SDK requires the JSON PHP extension';
        }

        if (!function_exists('http_build_query')) {
            $this->showError[] = 'Amplify PHP SDK requires http_build_query()';
        }
    }

    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
    );

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function setProjectId($projectId) {
        $this->projectId = $projectId;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    public function setPublicationUrl() {
        $this->publicationUrl = "http://" . $this->host . "/" . $this->version . "/";
    }

    public function getPublicationUrl() {
        return $this->publicationUrl;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }
    
    public function getRequestUrl() {
        return $this->requestUrl;
    }

    public function setRequestUrl($requestUrl) {
        $this->requestUrl = $requestUrl;
    }

    public function setTimeStamp($timeStamp) {
        $this->timeStamp = $timeStamp;
    }

    public function getTimeStamp() {
        $timeStamp = $this->timeStamp;
        if (empty($timeStamp))
            $this->setTimeStamp(time());
        return $this->timeStamp;
    }

    public function setOtt() {
        if (isset($_COOKIE['_ampUITN']) && !empty($_COOKIE['_ampUITN'])) {
            $this->ott =$_COOKIE['_ampUITN'];
        }
    }

    public function getOtt() {

        return $this->ott;
    }

    public function makeParams($params = false) {
        
        if (!is_array($params) && !empty($params))
            $this->showError[] = "paramter should be associative array!";
        $this->setOtt();
        if (isset($this->ott)) {
            $params['token'] = $this->getOtt();
        }
       
        if((!isset($params['identifiers']) && isset($_COOKIE['ampUser'])|| empty($params['identifiers']) && isset($_COOKIE['ampUser'])) && !isset($_SESSION['ampUser'])){
        
            $params['identifiers'] = json_decode(base64_decode($_COOKIE['ampUser']));
        }else if(isset ($_SESSION['ampUser']) && !isset($params['identifiers'])|| empty($params['identifiers']) && isset($_SESSION['ampUser'])){
        
            $params['identifiers'] = $_SESSION['ampUser'];
        }
        //print_r($params['identifiers']);die();
        try {
            if (!isset($params['apikey']))
                $params['apikey'] = $this->getApiKey();
            if (!isset($params['project_id']))
                $params['project_id'] = $this->getProjectId();

            $paramUrl = json_encode($params);
            $this->setParams($paramUrl);
        } catch (Exception $ex) {
            $this->showError[] = $ex->getCode() . ":" . $ex->getMessage();
        }
    }

    function http_call($functionName, $argumentsArray) {
            
            $apiKey = $this->getApiKey();
            $projectId = $this->getProjectId();
            
            if (empty($apiKey))
                $this->showError[] = "Invalid Api call, Api key must be provided!";
            if (empty($projectId))
                $this->showError[] = "Invalid Api call, Project Id must be provided!";
            if (!isset($this->functionUrlMap[$functionName]))
                $this->showError[] = "Invalid Function call!";
            try {
                
                $requestUrl = $this->getPublicationUrl() . $this->functionUrlMap[$functionName]; //there should be error handling to make sure function name exist
                if (isset($argumentsArray) && is_array($argumentsArray) && count($argumentsArray) > 0) {
                    $argumentsArray['useragent'] = $_SERVER['HTTP_USER_AGENT'];
                    $argumentsArray['ip'] = $_SERVER['REMOTE_ADDR'];
                    $argumentsArray['referrer'] = $_SERVER['HTTP_REFERER'];
                    
                    $this->makeParams($argumentsArray);
                }
               
                $paramdata=$this->getParams();
                return $this->makeRequest($requestUrl,$paramdata);
            } catch (Exception $ex) {
                $this->showError[] = $ex->getCode() . ":" . $ex->getMessage();
            }
    }

    protected function makeRequest($requestUrl,$data="", $ch = null) {
      
        if (!$ch) {
            $ch = curl_init();
        }
        $options = self::$CURL_OPTS;
      
        $options[CURLOPT_URL] = $requestUrl;
        $options[CURLOPT_POSTFIELDS] = $data;
        $options[CURLOPT_CUSTOMREQUEST] = "POST";
        $options[CURLOPT_HTTPHEADER] = array('Content-Type:application/json');

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
//        print_r($data);
//        print_r($result);
//        die();
        if ($result === false) {
            $this->showError[] = 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        $retrun = json_decode($result, true);
        if ($retrun['responseCode'] == '500')
            $this->showError[] = $retrun;
        return $retrun;
    }

    public function identify($user) {
            
        $argumentsArray = array(
                    'identifiers' => $user
                );
        
        $response = $this->http_call('identify', $argumentsArray);
        
        if ($response['responseCode'] == '200') {
            
            if ($user['email'] != ''){
                
                setcookie('ampUser', base64_encode(json_encode($user)), time() + 604800, '/');
                setcookie('ampUITN',"");
                
            }
        }

        return $response;
    }

    public function event($argumentsArray) {
        //print_r($argumentsArray);die();
        return $this->http_call('event', $argumentsArray);
    }

    public function customer_action($actionDescription) {
        $argumentsArray = $actionDescription;
        return $this->http_call('customer_action', $argumentsArray);
    }

    protected function deviceDetector() {
        if (stripos($_SERVER['HTTP_USER_AGENT'], "Android") && stripos($_SERVER['HTTP_USER_AGENT'], "mobile")) {
            $this->deviceDetect = 'android mobile';
        } else if (stripos($_SERVER['HTTP_USER_AGENT'], "Android")) {
            $this->deviceDetect = 'android tablet';
        } else if (stripos($_SERVER['HTTP_USER_AGENT'], "iPhone")) {
            $this->deviceDetect = 'iphone';
        } else if (stripos($_SERVER['HTTP_USER_AGENT'], "iPad")) {
            $this->deviceDetect = 'ipad';
        } else if (stripos($_SERVER['HTTP_USER_AGENT'], "mobile")) {
            $this->deviceDetect = 'generic mobile';
        } else if (stripos($_SERVER['HTTP_USER_AGENT'], "tablet")) {
            $this->deviceDetect = 'generic tablet';
        } else {
            $this->deviceDetect = 'desktop';
        }
    }

    public function describe() {
         return  $this->showError;
    }

}
}

?>