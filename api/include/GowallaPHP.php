<?php
/* 	Gowalla PHP Library

**  Class to integrate with Gowalla's API
* 
* 	Written by Greg Avola (http://twitter.com/gregavola)
*
* 	For more documentation please refer to http://code.google.com/p/gowallaphp/
*
* 	GowallaPHP.php - the class.
*
*   Version 1.60
*/

class GowallaPHP {

	public $userAgent = "GowallaPHP 1.60 (http://code.google.com/p/gowallaphp/)";
	public $apiBase;
	public $client_id;
	public $redirect_uri;
	public $access_token;
	public $client_secret;
	public $refresh_token;
	public $expire_in;
	public $auth_type;
	public $token_url;
	public $scope;
	public $username;
	public $refresh_time;
	
	protected $grant_type = "authorization_code";
	protected $oauth_url = "https://gowalla.com/api/oauth/";	
	
	/* Constructors */
	
	public function __construct($client_id, $callback_url=null, $client_secret=null) 
	{
		if ($callback_url == null && $client_secret== null)
		{
			$this->auth_type = "basic";
			$this->client_id = $client_id;
			$this->apiBase = "http://api.gowalla.com/";
		}
		else
		{
			$this->auth_type = "oauth";
			$this->client_id = $client_id;
			$this->redirect_uri = $callback_url;
			$this->client_secret = $client_secret;	
			$this->apiBase = "https://api.gowalla.com/";
		}

	}
	
	/* Utilities */
			
	public function getUserAgent()
	{
		return $this->userAgent;
	}
	
	public function setUserAgent($user_agent)
	{
		$this->userAgent = $user_agent;
	}
	
	public function getToken()
	{
		$tokens["access_token"] = $this->access_token;
		$tokens["refresh_token"] = $this->refresh_token;
		$tokens["username"] = $this->username;
		$tokens["refresh_time"] = $this->refresh_time;
		
		return $tokens;
	}
			
	/* OAuth functions */
	
	public function generate_url($post = false)
	{
		if ($this->auth_type == "basic")
		{
			$message = "This instance is set for Basic HTTP Authentication. To use Oauth you must re-initialize this class.";
			GowallaException::raise_key($message);
		}
		else
		{
			
			$url = $this->oauth_url . "new?redirect_uri=" . $this->redirect_uri . "&client_id=" . $this->client_id . "&type=" . $this->grant_type;
			
			if ($post)
			{
				$url .= "&scope=read-write";
			}

			return $url;
		}

	}
	
	public function removeToken()
	{
		$this->access_token = "";
		$this->refresh_token = "";
	}
	
	public function storeToken($code, $post = false)
	{
		if ($this->auth_type == "basic")
		{
			$message = "This instance is set for Basic HTTP Authentication. To use Oauth you must re-initialize this class.";
			GowallaException::raise_key($message);
		}
		else
		{
		
			
			$parms["code"] = $code;
			$parms["client_id"] = $this->client_id;
			$parms["client_secret"] = $this->client_secret;
			$parms["redirect_uri"]= $this->redirect_uri;
			$parms["type"] = $this->grant_type;
			
			if ($post)
			{
				$parms["scope"] = "read-write";
			}

			$this->parse_and_store($this->oauth($parms));
		}
		
	}
	
	public function setToken($code = null, $refresh = null, $expire_at = null)
	{
		if ($code == null || $refresh == null || $expire_at == null)
		{
			$message = "Either the Access Token or Refresh Token was not passed.";
			GowallaException::raise_key($message);
		}
		else
		{
			$x = (int)date("U", strtotime($expire_at));
			
			if ($x == 0)
			{
				$message = "Expiration Date must be a valid Date. You passed: " . $expire_at;
				GowallaException::raise_key($message);
			}
			else
			{
				$this->access_token = $code;
				$this->refresh_token = $refresh;
				$this->refresh_time = $x;
			}
		}
	
	}
	
	protected function refresh($url="")
	{
		$parms["refresh_token"] = $this->refresh_token;
		$parms["client_id"] = $this->client_id;
		$parms["client_secret"] = $this->client_secret;
		$parms["grant_type"] = "refresh_token";
		$parms["access_token"] = $this->access_token;
		
		$this->parse_and_store($this->refresh_oauth($parms), $url);
	}
	
	protected function parse_and_store($result, $url = "")
	{		
		$x = (int)date("U", strtotime($result->expires_at));

		$this->access_token = $result->access_token;
		$this->refresh_token = $result->refresh_token;
		$this->expire_in = $result->expires_in;
		$this->scope = $result->scope;
		$this->username = $result->username;
		$this->refresh_time = $x;
		
		if ($url != "")
		{
			return $this->call($url);
		}
	}	
	
	
	public function test()
	{
		$url = $this->oauth_url . "echo";

		return $this->call($url);
	}
	
	/* Spots Functions */
	
	public function get_spot($id)
	{
		$url = $this->apiBase . "spots/". $id;
		
		return $this->call($url);
	}
	
	public function get_spot_activites($id)
	{
		$url = $this->apiBase . "spots/". $id . "/events";
		
		return $this->call($url);
	}
	
	public function get_spot_items($id)
	{
		$url = $this->apiBase . "spots/". $id . "/items";
		
		return $this->call($url);
	}
	
	public function get_spot_photos($id)
	{
		$url = $this->apiBase . "spots/". $id . "/photos";
		
		return $this->call($url);
	}
	
	public function get_spots($lat, $lng, $radius = 50)
	{
		$url = $this->apiBase . "spots?lat=".$lat."&lng=".$lng."&radius=" . $radius;
		
		return $this->call($url);
	}
	
	public function get_categories()
	{
		$url = $this->apiBase . "categories";
		
		return $this->call($url);
	}
	
	public function get_category($id)
	{
		$url = $this->apiBase . "categories/" . $id;
		
		return $this->call($url);
	}	

	/* Users */
	
	public function get_user($user)
	{
		$url = $this->apiBase . "users/" . $user;
		
		return $this->call($url);
	}
	
	public function get_user_stamps($user, $page = 1)
	{
		$url = $this->apiBase . "users/" . $user . "/stamps?page=" . $page;
	
		
		return $this->call($url);
	}
	
	public function get_user_top_spots($user)
	{
		$url = $this->apiBase . "users/" . $user . "/top_spots";
		
		return $this->call($url);
	}
	
	public function get_users_items($user,  $page = 1)
	{
		$url = $this->apiBase . "users/" . $user . "/items?page=" . $page;
		
		return $this->call($url);
	} 
	
	public function get_users_missing_items($user, $page)
	{
		$url = $this->apiBase . "users/" . $user . "/items/missing?page=" . $page;
		
		return $this->call($url);
	}
	
	public function get_users_vault_items($user, $page)
	{
		$url = $this->apiBase . "users/" . $user . "/items/vault?page=".$page;
		
		return $this->call($url);
	}
	
	public function get_user_photos($user)
	{
		$url = $this->apiBase . "users/". $user . "/photos";
		
		return $this->call($url);
	}
	
	public function get_user_events($user)
	{
		$url = $this->apiBase . "users/". $user . "/events";
		
		return $this->call($url);
	}
	
	/* Items */
	
	public function get_item_detail($item)
	{
		$url = $this->apiBase . "item/" . $item;
		
		return $this->call($url);
	}
	
	/* Trips */
	
	public function get_trips()
	{
		$url = $this->apiBase . "trips";
		
		return $this->call($url);
	}
	
	public function get_trip_details($trip)
	{
		$url = $this->apiBase . "trips/" . $trip;
		
		return $this->call($url);
	}
		
	/* Advanced Functions */
	
	public function get_user_friends($user)
	{
		$url = $this->apiBase . "users/" . $user . "/friends"; 
		
		return $this->call($url);
	}	
	
	/* Check-in */
	
	public function check_in_test($lat="", $lng="", $spot_id="", $comment = "", $post_to_twitter = false, $post_to_facebook = false)
	{
		if ($lat == "" || $lng == "" || $spot_id == "")
		{
			$message = "Latitude/Longitude or Spot ID not passed.";
			GowallaException::raise_key($message);
		}
		else
		{
			$parms = array("lat" => $lat, "lng" => $lng, "spot_id" => $spot_id, "comment" => $comment, "post_to_twitter" => $post_to_twitter, "post_to_facebook" => $post_to_facebook, "oauth_token" => $this->access_token);
						
			$url = $this->apiBase. "checkins/test";
			
			return $this->check_in_oauth($url, $parms);
		}
		
	}
	
	public function check_in($lat="", $lng="", $spot_id="", $comment = "", $post_to_twitter = false, $post_to_facebook = false)
	{
		if ($lat == "" || $lng == "" || $spot_id == "")
		{
			$message = "Latitude/Longitude or Spot ID not passed.";
			GowallaException::raise_key($message);
		}
		else
		{
			$parms = array("lat" => $lat, "lng" => $lng, "spot_id" => $spot_id, "comment" => $comment, "post_to_twitter" => $post_to_twitter, "post_to_facebook" => $post_to_facebook, "oauth_token" => $this->access_token);
						
			$url = $this->apiBase. "checkins/";
			
			return $this->check_in_oauth($url, $parms);
		}
		
	}
	
	protected function check_in_oauth($url, $parms)
	{
		if (!is_array($parms) || !isset($parms['oauth_token']) || $parms["oauth_token"] == "") 
		{
			$message = "Code has not been set";
			GowallaException::raise_key($message);	
		}
		else
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);	
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $parms);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			
			$code = array();
		    $response = curl_exec($ch);

		   	$responseInfo=curl_getinfo($ch);
		
		    curl_close($ch);

			$httpCode = (int)$responseInfo["http_code"];

			$values = json_decode($response);
			
	
			if ($httpCode != 200)
			{
				GowallaException::raise($url, $httpCode, $values);
			}
			else
			{
				if ($values->error)
				{
					GowallaException::raise($url, 235, $values->error);
				}
				else
				{
					return $values;
				}
			}
		}
	}
	
	protected function refresh_oauth($parms)
	{
		if (!is_array($parms) || !isset($parms['refresh_token']) || $parms["refresh_token"] == "") 
		{
			$message = "Refresh Token has not been set";
			GowallaException::raise_key($message);	
		}
		else
		{
			$url = $this->oauth_url . "token";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);	
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $parms);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			
			$code = array();
		    $response = curl_exec($ch);

		   	$responseInfo=curl_getinfo($ch);
		
		    curl_close($ch);

			$httpCode = (int)$responseInfo["http_code"];

			$values = json_decode($response);
			
			if ($httpCode != 200 || $httpCode != "200")
			{
				
				GowallaException::raise($url, $httpCode, $values->error);
			}
			else
			{
				if ($values->error)
				{
					GowallaException::raise($url, 235, $values->error);
				}
				else
				{
					return $values;
				}
			}
		}
	}
	
	protected function oauth($parms)
	{
		if (!is_array($parms) || !isset($parms['code']) || $parms["code"] == "") 
		{
			$message = "Code has not been set";
			GowallaException::raise_key($message);	
		}
		else
		{
			$url = $this->oauth_url . "token";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);	
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $parms);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			
			$code = array();
		    $response = curl_exec($ch);

		   	$responseInfo=curl_getinfo($ch);
		
		    curl_close($ch);

			$httpCode = (int)$responseInfo["http_code"];

			$values = json_decode($response);
			
			if ($httpCode != 200)
			{
				GowallaException::raise($url, $httpCode, $values->error);
			}
			else
			{
				if ($values->error)
				{
					GowallaException::raise($url, 235, $values->error);
				}
				else
				{
					return $values;
				}
			}
		}
	}
	
	protected function call($url)
	{	
		if (($this->access_token == "" || $this->access_token == null) && $this->auth_type == "oauth")
		{
			$message = "OAuth Token is not set.";
			GowallaException::raise_key($message);
		}
		elseif (($this->client_id == "" || $this->client_id == null) && $this->auth_type == "basic")
		{
			$message = "API Key is not set.";
			GowallaException::raise_key($message);
		}
		else
		{
			if ($this->auth_type == "oauth")
			{
				$current_time = (int)date("U");
				
				if(stristr($url, '?'))
				{
					$url .= "&access_token=" . $this->access_token;
				}
				else
				{
					$url .= "?access_token=" . $this->access_token;
				}
				
				if ((int)$this->refresh_time == 0 || $this->refresh_time == null)
				{
					$message = "No refresh time stored. Please use setTokens to set the refresh time as Date Object";
					GowallaException::raise_key($message);
				}
				else
				{
					if ($current_time > (int)$this->refresh_time)
					{
						$this->refresh($url); 
					}
				}				
			}

			$ch = curl_init($url);	
		    curl_setopt($ch, CURLOPT_VERBOSE, 1);
		    curl_setopt($ch, CURLOPT_NOBODY, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
			
			if ($this->auth_type == "oauth")
			{
				curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
				curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
			}
			else
			{
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
				                "X-Gowalla-API-Key: " . $this->client_id,  
				                "Accept: application/json"  
				            ));
			}
			
		    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$code = array();
		    $response = curl_exec($ch);
		   	$responseInfo=curl_getinfo($ch);
		    curl_close($ch);
			
			$httpCode = (int)$responseInfo["http_code"];

			$values = json_decode($response);

			if ($httpCode != 200)
			{
				GowallaException::raise($url, $httpCode, $values->error);
			}
			else
			{
				if ($values->error)
				{
					GowallaException::raise($url, 235, $values->error);
				}
				else
				{
					return $values;
				}
			}
		}
	}
	
}

class GowallaException extends Exception
{
	public static function raise($url, $httpCode, $error_message)
	{
	    switch($httpCode)
	    {
	      case 400:
			$message = "Resource Invalid (improperly formatted request) Request: " . $url;
	        throw new GowallaResourceInvalidException($message, $httpCode);
		  case 403:
			$message = $error_message->message;
			throw new GowallaCheckInException($message, $httpCode);
	      case 401:
			$message = "Unauthorized (incorrect or missing authentication credentials) Request: " . $url;
	        throw new GowallaUnauthorizedException($message, $httpCode);
	      case 404:
			$message = "Resource Not Found Request: " . $url;
	        throw new GowallaNotFoundException($message, $httpCode);
	      case 405:
			$method = "Method not allowed Request: " . $url;
	        throw new GowallaMethodException($message, $httpCode);
	      case 500:
			$message = "Application Error Request: " . $url;
			throw new GowallaApplicationException($message, $httpCode);
	 	  default:
			$message = "Unknown Error Request: " . $url;
	        throw new GowallaException($error_message, $httpCode);
	    }
	}
	
	public static function raise_date($message)
	{
		throw new GowllaDateRangeException($message);
	}
	
	public static function raise_key($message)
	{
		throw new GowallaAPIKeyException($message);
	}
}
	class GowallaResourceInvalidException extends GowallaException{}
	class GowallaUnauthorizedException extends GowallaException{}
	class GowallaNotFoundException extends GowallaException{}
	class GowallaMethodException extends GowallaException{}
	class GowallaApplicationException extends GowallaException{}
	class GowllaDateRangeException extends GowallaException{}
	class GowallaAPIKeyException extends GowallaException{}
	class GowallaCheckInException extends GowallaException{}

	
?>