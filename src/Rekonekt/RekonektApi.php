<?php


namespace Rekonekt;


class RekonektApi extends RekonektBaseApi{
	/**
	 * Session key where we store current users state
	 * @var string
	 */
	protected $sessionKey = 'rekonekt_api_key';
	/**
	 * Is session started
	 * @var bool
	 */
	protected $isSessionStarted = false;

	public function __construct($apiKey = null){
		parent::__construct($apiKey);

		$this->startSession();

		// If no API key is given, try to load it from session
		if($apiKey === null){
			$sessionApiKey = $this->sessionGet($this->sessionKey);
			if($sessionApiKey){
				$this->setApiKey($sessionApiKey);
			}
		}

	}

	/**
	 * Get all user employments
	 * @param string $email
	 * @param string $password
	 * @return array
	 */
	public function getEmployment($email, $password){
		return $this->callPost('user/employment', array(
			'email' => $email,
			'password' => $password,
		));
	}

	/**
	 * Get employees API key and set it to this API request
	 * @param string $email
	 * @param string $password
	 * @param int $employeeId
	 * @return array
	 */
	public function loginEmployee($email, $password, $employeeId){
		$data = $this->callPost('user/login', array(
			'email' => $email,
			'password' => $password,
			'employeeId' => (int)$employeeId,
		));

		$apiKey = $data['apiKey']['apiKey'];

		$this->setApiKey($apiKey);
		$this->sessionSet($this->sessionKey, $apiKey);

		return $data;
	}

	/**
	 * Log out current
	 * @return bool
	 */
	public function logoutEmployee(){
		$data = $this->callPost('user/logout');

		$this->setApiKey(null);
		$this->sessionUnset($this->sessionKey);

		return (bool)$data['loggedOut'];
	}

	/**
	 * Get value from session
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	protected function sessionGet($key, $default = null){
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
	}

	/**
	 * Set value to session
	 * @param string $key
	 * @param mixed $value
	 */
	protected function sessionSet($key, $value){
		$_SESSION[$key] = $value;
	}

	/**
	 * Unset value from session
	 * @param string $key
	 */
	protected function sessionUnset($key){
		unset($_SESSION[$key]);
	}

	/**
	 * Start session, if needed
	 * @return bool
	 */
	protected function startSession(){
		if(!$this->isSessionStarted){
			$this->isSessionStarted = session_start();
		}
		return $this->isSessionStarted;
	}
}