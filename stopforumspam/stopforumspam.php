<?php


class stopspam {
	
	protected $_baseUrl = "http://www.stopforumspam.com/api?f=xmldom&";
	
	protected $_timeOut = 3;
	
	protected $_apiKey = "";

	public function __construct(){
	}
	
	/**
	 * Get the data from stopforumspam.com
	 * 
	 * @param string $appendToUrl The parameter which identifies the call type
	 * @throws StopspamException
	 * @return boolean|string
	 */
	protected function fetchData($appendToUrl){
		$client = curl_init();
		curl_setopt($client, CURLOPT_URL, $this->_baseUrl . $appendToUrl);
		curl_setopt($client, CURLOPT_TIMEOUT, $this->_timeOut);
		curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($client);
		curl_close($client);
		
		if(false === $response){
			throw new StopspamException("curl Exception: " . curl_error($client) . ":" . curl_errno($client), 20);
		}

		return $response;
	}
	
	/**
	 * Parse the output of stopforumspam.com
	 * 
	 * @param string $request The parameter which identifies the call type
	 * @throws StopspamException
	 * @return boolean
	 */
	protected function getResponse($request){
		$data = simplexml_load_string($this->fetchData($request));
		$status = false;
		
		$part = substr($request, 0, strpos($request, "="));
		
		if($data instanceof SimpleXMLElement){
			if(1 == $data->success){
				if(1 == $data->$part->appears)
					$status = true;
			} else {
				throw new StopspamException("www.stopforumspam.com told us the request was no success", 30);				
			}
		} else {
			throw new StopspamException("Data got from www.stopforumspam.com is not correct XML data", 10);
		}
		return $status;
	}
	
	/**
	 * Set the maximum timeout for the webrequest.
	 * when the timeout is trigger an Exception is thrown.
	 * 
	 * @param int $timeOut
	 */
	public function setTimeout($timeOut){
		$this->_timeOut = $timeOut;
	}
	
	
	/**
	 * Check if the email adress is ussed for spamming websites.
	 * Returns yes if the address is registered on stopforumspam.com 
	 * 
	 * @param string $email
	 * @return boolean
	 */
	public function isSpamEmail($email){
		return $this->getResponse('email=' . $email);
	}
	
	/**
	 * Check if the ip adress is ussed for spamming websites. 
	 * Returns yes if the address is registered on stopforumspam.com 
	 * 
	 * @param string $ip
	 * @return boolean
	 */
	public function isSpamIp($ip){		
		return $this->getResponse('ip=' . $ip);
	}
	
	/**
	 * Check if the username is ussed for spamming websites.
	 * Returns yes if the address is registered on stopforumspam.com  
	 * 
	 * @param stringe $username
	 * @return boolean
	 */
	public function isSpamUsername($username){
		return $this->getResponse('username=' . $username);		
	}
	
	public function commitSpam($apiKey, $username, $ip, $email, $evidence){
		 $poststring = "username=" . $username . "&ip_addr=" . $ip . "&email=" . $email . "&api_key=" . $apiKey . "&evidence=" . $evidence;
		 $client = curl_init(POSTURL);
		 curl_setopt($client, CURLOPT_POST ,1);
		 curl_setopt($client, CURLOPT_POSTFIELDS, $poststring);
		 curl_setopt($client, CURLOPT_FOLLOWLOCATION, 1);
		 curl_setopt($client, CURLOPT_HEADER, 0);
		 curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
		 $Rec_Data = curl_exec($client);		
	}
	
}

class StopspamException extends Exception { };