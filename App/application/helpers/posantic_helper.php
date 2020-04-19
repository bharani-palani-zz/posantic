<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('is_this_subdomain_browser'))
{
    function is_this_subdomain_browser($subdomain)
    {
		$parsedUrl = parse_url($_SERVER['HTTP_HOST']);
		$host = explode('.', $parsedUrl['path']);
		$subdomain_array = array_slice($host, 0, count($host) - 2 );
		$browser_subdomain = $subdomain_array[0];
		if($subdomain != $browser_subdomain)
		{
			return false;
		} else {
			return true;
		}

    }   
}

?>