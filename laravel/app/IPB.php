<?php

namespace App;

use App\Settings;

class IPB {

	/*
	 * Authorization
	 * @return \IPS\Member|false
	 */
	public static function login($username, $password, $rememberMe=true, $anonymous=false)
	{
	    $login = new \IPS\Login(\IPS\Http\Url::internal(''));
	    /* старое 
		$login->forms();

	    try {
	        $member = $login->authenticateStandard(array(
	            'auth'     => $username,
	            'password' => $password,
	        ));
	    */
		
		$pass = eval('return new class
		{
			public function __toString()
			{
				return \'' . $password . '\';
			}
		};' );
		
		foreach ( $login->usernamePasswordMethods() as $method )
		{

			try {
				$member = $method->authenticateUsernamePassword($login, $username, $pass);
				
if ( $member === TRUE ){
					$member = $this->reauthenticateAs;
				}
				
				if ( $member )
				{
					$success = new \IPS\Login\Success( $member, $method, $rememberMe, $anonymous  );
						
					break;
				}
			
			} 
		
			catch (\IPS\Login\Exception $e) {
				$success = false;
			}
		}
if($success){
	    if ($anonymous and !\IPS\Settings::i()->disable_anonymous) {
	        \IPS\Session::i()->setAnon();
	        \IPS\Request::i()->setCookie('anon_login', 1);
	    }

	    \IPS\Session::i()->setMember($member);
		$device = \IPS\Member\Device::loadOrCreate($member);

        $member->last_visit = $member->last_activity;
        $member->save();

        $device->anonymous = false;
        $device->updateAfterAuthentication($rememberMe, null);

        $member->memberSync('onLogin');
        $member->profileSync();
		/*
	    if ($rememberMe) {
	        $expire = new \IPS\DateTime;
	        $expire->add(new \DateInterval('P70D'));
	        \IPS\Request::i()->setCookie('member_id', $member->member_id, $expire);
	        \IPS\Request::i()->setCookie('pass_hash', $member->member_login_key, $expire);

	        if ($anonymous and !\IPS\Settings::i()->disable_anonymous) {
	            \IPS\Request::i()->setCookie('anon_login', 1, $expire);
	        }
	    }
*/
	   //$member->memberSync('onLogin', array( \IPS\Login::getDestination() ));
}
	    return $success?$member->apiOutput():false;
	}


	public static function groups()
	{
		$groups = IPB::api('/core/groups');

		return !empty($groups['results']) ? $groups['results'] : null;
	}

	/*
	 * Logout
	 * @return void
	 */
	public static function logout()
	{
	    $redirectUrl = \IPS\Http\Url::internal('');
	    $member = \IPS\Member::loggedIn();

	    /* Are we logging out back to an admin user? */
	    if (isset($_SESSION['logged_in_as_key'])) {
	        $key = $_SESSION['logged_in_as_key'];
	        unset(\IPS\Data\Store::i()->$key);
	        unset($_SESSION['logged_in_as_key']);
	        unset($_SESSION['logged_in_from']);

	        return;
	    }

	    \IPS\Request::i()->setCookie('member_id', null);
	    \IPS\Request::i()->setCookie('pass_hash', null);
	    \IPS\Request::i()->setCookie('anon_login', null);

	    foreach (\IPS\Request::i()->cookie as $name => $value) {
	        if (mb_strpos($name, "ipbforumpass_") !== false) {
	            \IPS\Request::i()->setCookie($name, null);
	        }
	    }

	    session_destroy();

	    /* Login handler callback */
	    foreach (\IPS\Login::handlers(true) as $k => $handler) {
	        try {
	            $handler->logoutAccount($member, $redirectUrl);
	        } catch (\BadMethodCallException $e) {

			}
	    }

	    /* Member sync callback */
	    $member->memberSync('onLogout', array($redirectUrl));
	}

	public static function api($route, $params = [])
	{
		$api_url = trim(Settings::getByKey('api_url'), '/');
		$route = trim($route, '/');

		$communityUrl = $api_url . '/' . $route . '?' . http_build_query($params);
		$apiKey = Settings::getByKey('api_key');


		$curl = \curl_init( $communityUrl );
		curl_setopt_array( $curl, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPAUTH	   => CURLAUTH_BASIC,
			CURLOPT_USERPWD		   => "{$apiKey}:",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_RETURNTRANSFER => 1
		));

		$response = curl_exec( $curl );

		return json_decode($response, 1);
	}
}
