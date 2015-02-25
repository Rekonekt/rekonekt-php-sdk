<?php
namespace Rekonekt;

class RekonektBaseApi{
	/**
	 * @var string Your API key
	 */
	protected $apiKey;

	/**
	 * @var string API URL, with trailing slash
	 */
	protected $apiUrl = 'http://www.rekonekt.com/api/v1/';

	/**
	 * @param string $apiKey API key
	 */
	public function __construct($apiKey = null){
		$this->apiKey = $apiKey;
	}

	/**
	 * Set current API key
	 * @param string $apiKey
	 * @return self
	 */
	public function setApiKey($apiKey){
		$this->apiKey = $apiKey;
		return $this;
	}

	/**
	 * Get current API key
	 * @return null|string
	 */
	public function getApiKey(){
		return $this->apiKey;
	}

	/**
	 * Set current
	 * @param string $apiUrl
	 * @return self
	 */
	public function setApiUrl($apiUrl){
		$this->apiUrl = $apiUrl;
		return $this;
	}

	/**
	 * @param string $requestMethod
	 * @param string $apiMethod
	 * @param array $getData
	 * @param array $postData
	 * @return array
	 * @throws ApiException
	 */
	public function callMethod($requestMethod, $apiMethod, $getData = array(), $postData = array()){
		if(!function_exists('json_decode')){
			throw new ApiException('Please enable php json extension');
		}

		if($this->apiKey){
			$getData += array(
				'apiKey' => $this->apiKey,
			);
		}

		$url = $this->apiUrl . $apiMethod;

		$response = $this->getUrlContent($url, $requestMethod, $getData, $postData);

		$result = json_decode($response, true);

		if(!empty($result['error'])){
			throw new ApiException('API error: ' . $result['error']);
		}

		return $result;
	}

	/**
	 * @param string $urlDomain Domain + path WITHOUT query part
	 * @param string $requestMethod get or post
	 * @param array $queryData
	 * @param array $postData
	 * @return mixed
	 * @throws ApiException
	 */
	protected function getUrlContent($urlDomain, $requestMethod, array $queryData = array(), array $postData = array()){
		$isPost = $requestMethod == 'post';
		$urlDomain .= '?' . http_build_query($queryData);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $urlDomain);
		curl_setopt($ch, CURLOPT_POST, $isPost ? 1 : 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		if($isPost){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		$error = curl_error($ch);

		curl_close($ch);

		if(!$output){
			throw new ApiException('Could not HTTP request: ' . var_export($error, true));
		}

		return $output;
	}

	/**
	 * Call GET API request
	 * @param $action
	 * @param array $queryData
	 * @return array
	 * @throws ApiException
	 */
	public function callGet($action, $queryData = array()){
		return $this->callMethod('get', $action, $queryData);
	}

	/**
	 * Call POST API request
	 * @param string $action
	 * @param array $postData
	 * @return array
	 * @throws ApiException
	 */
	public function callPost($action, $postData = array()){
		return $this->callMethod('post', $action, array(), $postData);
	}
}