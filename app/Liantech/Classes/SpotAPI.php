<?php

namespace App\Liantech\Classes;

use \GuzzleHttp\Client;

/**
* Spot Option API main communicator
*/
class SpotAPI
{
	
	/**
	 * Main Spot API URL
	 * @var string
	 */
	public static $API_URL = 'https://api-spotplatform.72option.com/Api';//'https://api-spotplatform.ivoryoption.com/Api';


	protected $broker = "ivoryoption";
	/**
	 * API Username
	 * @var string
	 */
	protected $username;

	/**
	 * API Password
	 * @var string
	 */
	protected $password;

	/**
	 * Current user ip address
	 * @var string
	 */
	protected $userIp;

	/**
	 * Guzzle client
	 * @var Obj
	 */
	protected $client;

	/**
	 * API errors
	 * @var array
	 */
	protected $errors = [];

	function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
		$this->userIp = $this->getUserIp();

	}

	/**
	 * get the current user IP
	 * @return string 
	 */
	public function getUserIp()
	{
		$ipInspector = new IP_Inspector();
		return $ipInspector->getIp();
	}

	/**
	 * return the errors Array
	 * @return Array 
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * return the API username
	 * @return string 
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * return the API password
	 * @return string 
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * return the API username
	 * @return string 
	 */
	public function getBroker()
	{
		return $this->broker;
	}

	/**
	 * return the API username
	 * @return string 
	 */
	public function setBroker( $broker )
	{
		$this->broker = $broker;

		if($broker == "72option"){
			$this->username = env('SPOT_72_USERNAME', '');
			$this->password = env('SPOT_72_PASSWORD', '');
		}else{
			$this->username = env('SPOT_USERNAME', '');
			$this->password = env('SPOT_PASSWORD', '');
		}

		return $this;
	}

	/**
	 * return false if no errors exists
	 * @return boolean 
	 */
	public function hasErrors()
	{
		return !! count($this->errors) > 0;
	}


	public function callWithModuleAndCommand($module, $command, $params)
	{

		/**
		 * Set the module and the command
		 * for SpotAPI
		 * 
		 * @var Array
		 */
		$moduleCommand = [
			"MODULE"	=>	$module,
			"COMMAND"	=>	$command,
		];

		/**
		 * Set the username and password
		 * to acces the SpotAPI
		 * 
		 * @var Array
		 */
		$credentials = [
			'api_username' => $this->username,
			'api_password' => $this->password,
		];

		/**
		 * build http request with all the data
		 * @var [type]
		 */
		$requestData = http_build_query(array_merge($credentials, $moduleCommand, $params));

		$ch = curl_init("https://api-spotplatform." . $this->getBroker() . ".com/Api");
		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($ch);

		curl_close($ch);

		$parsedData = new \SimpleXMLElement($res);

		return $parsedData;

	}

	public function addBonusToCustomersByEmails($emails = [], $amount)
	{
		if( !is_array($emails) )
			return false;

		foreach ($emails as $email) {
			$this->addBonusToEmail($email, $amount);
		}
	}

	public function getCustomerByEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

		    $response =  $this->callWithModuleAndCommand("Customer", "view", [ 'FILTER[email]' => $email ]);

		    if( !isset($response->Customer->data_0) )
                return false;

            $customer = (array) $response->Customer->data_0;

            return $customer;
		}

		return false;
	}

	public function getCustomerById($id)
	{
		if ( is_numeric($id) ) {

		    $response =  $this->callWithModuleAndCommand("Customer", "view", [ 'FILTER[id]' => $id ]);


		    if( !isset($response->Customer->data_0) )
                return false;

            $customer = (array) $response->Customer->data_0;

            return $customer;
		}

		return false;
	}

	public function addBonusToEmail($email, $amount)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		    $response = $this->callWithModuleAndCommand("Customer", "view", [ 'FILTER[email]' => $email ]);

		    if( !isset($response->Customer->data_0) )
		    	return false;

		    $customer = (array) $response->Customer->data_0;

		    $response = $this->callWithModuleAndCommand("CustomerDeposits", "add", [ 
		    	'customerId' => $customer["id"], 
		    	'method' => 'bonus', 
		    	'amount' => $amount
		    ]);

		}

		return true;
		
	}


}