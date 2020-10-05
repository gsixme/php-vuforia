<?php

namespace Gsix\Vuforia\Request;

use DateTime;
use DateTimeZone;
use Exception;
use JsonSerializable;
use HTTP_Request2;
use HTTP_Request2_Exception;

abstract class AbstractRequest
{
	protected $api_base;
	protected $access_key;
	protected $secret_key;
	protected $naming_rule;
	protected $max_meta_size;
	protected $max_image_size;

	public function __construct(array $config)
	{
		$this->api_base = $config['api_base'];
		$this->access_key = $config['access_key'];
		$this->secret_key = $config['secret_key'];
		$this->naming_rule = $config['naming_rule'];
		$this->max_meta_size = $config['max_meta_size'];
		$this->max_image_size = $config['max_image_size'];
	}

	protected function request(
		$method = HTTP_Request2::METHOD_GET,
		$uri, 
		$body = null, 
		$headers = null
	) {
        
        if(empty($this->access_key) || empty($this->secret_key)) {
            throw new Exception('Missing Vuforia Access/Secret Key(s)');
        }
        
        $request = new HTTP_Request2();
        $request->setMethod($method);
        $request->setConfig(['ssl_verify_peer' => false]);
        $request->setURL($this->api_base . $uri);
        
        if(!empty($body)) {
            $request->setBody($body);
		}

		// Set headers

		// Define the Date field using the proper GMT format
        $date = new DateTime("now", new DateTimeZone("GMT"));
		$request->setHeader('Date', $date->format("D, d M Y H:i:s") . " GMT" );

		if(!empty($headers)) {
            foreach ($headers as $key => $value) {
                $request->setHeader($key, $value);
            }
        }

        $signature = '';
        try {
            $signature = $this->sign($request);
        }
        catch(Exception $e) {
            return [
	            'status' => 500,
	            'message' => $e->getMessage(),
	            'data' => [],
            ];
        }
        
        $request->setHeader("Authorization" , "VWS " . $this->access_key . ":" . $signature);

		try {
            $response = $request->send();
            return [
	            'status' => $response->getStatus(),
	            'message' => 'OK',
	            'data' => json_decode($response->getBody()),
            ];
        } catch(HTTP_Request2_Exception $e) {
            return [
	            'status' => 500,
	            'message' => $e->getMessage(),
	            'data' => [],
            ];
        }
    }

    private function sign(HTTP_Request2 $request)
    {
    	$method = $request->getMethod();
        
        // The HTTP Header fields are used to authenticate the request
        $requestHeaders = $request->getHeaders();
        
        // note that header names are converted to lower case
        $dateValue = $requestHeaders['date'];
        
        $requestPath = $request->getURL()->getPath();
        
        $contentType = '';

        // Not all requests will define a content-type
        if(isset($requestHeaders['content-type'] )) {
            $contentType = $requestHeaders['content-type'];
        }
        
        $hexDigest = 'd41d8cd98f00b204e9800998ecf8427e';
        if ( $method == 'GET' || $method == 'DELETE' ) {
            // Do nothing because the strings are already set correctly
        } else if ( $method == 'POST' || $method == 'PUT' ) {
            // If this is a POST or PUT the request should have a request body
            $hexDigest = md5($request->getBody(), false );
        } else {
            throw new Exception("ERROR: Invalid content type");
        }
        
        $toDigest = "$method\n$hexDigest\n$contentType\n$dateValue\n$requestPath";
        
        $shaHashed = '';
        
        // the SHA1 hash needs to be transformed from hexidecimal to Base64
        $shaHashed = $this->hexToBase64( hash_hmac("sha1", $toDigest , $this->secret_key) );
        
        return $shaHashed;
    }
    
    private function hexToBase64($hex)
    {
        $return = '';
        
        foreach(str_split($hex, 2) as $pair){
            $return .= chr(hexdec($pair));
        }
        
        return base64_encode($return);
    }
}