<?php
	if (!class_exists('Configuration'))
	{
		define('DIR_SEPARATOR', '/');
		class	Configuration
		{
			public static $Version = '1-0';
			public static $Url = 'http://caveblachon.fr';
			public static $Mysql = array
			(
				'base' => 'blachon'
				, 'login' => 'blachon'
				, 'pwd' => ''
				, 'host' => 'localhost'
			);
			public static $Google = array
			(
				'GoogleAnalytics' => array(
				'Analytics' => null
				, 'AnalyticsAdmin' => null
				, 'Domain' => 'caveblachon.fr')
				, 'GoogleMap' => array(
				 'Maps' => ''
				, 'MapZoom' => '8'
				, 'MapPos' => '45.08649,4.813728')
				, 'GooglePlus' => array(
				 'ClientId' => ''
				, 'ClientSecret' => ''
				, 'RedirectUri' => 'http://caveblachon.fr/administration/identificationGoogle.php'
				, 'DeveloperKey' => '')
			);
			public static $Twitter = array
			(
				'consumer_key'    => ''
				, 'consumer_secret' => ''
			);
			public static $Facebook = array
			(
				'appId'  => ''
				, 'secret' => ''
			);
			public static $Images = array
			(
				'location' => '/srv/data-blachon/SiteWeb/images/'
				, 'cache' => '/srv/data-blachon/SiteWeb/cache/'
			);
			public static $Javascript = array
			(
				'location' => '/srv/data-blachon/SiteWeb/www/Scripts/'
				, 'cache' => '/srv/data-blachon/SiteWeb/cache/'
				, 'compact' => true
				, 'force' => false
			);
			public static $Css = array
			(
				'location' => '/srv/data-blachon/SiteWeb/www/Css/'
				, 'cache' => '/srv/data-blachon/SiteWeb/cache/'
				/*, 'compact' => true
				, 'force' => true*/
			);
			public static $Documents = array
			(
				'location' => '/srv/data-blachon/SiteWeb/documents/'
			);		
			public static $Administration = array
			(
				'location' => '/srv/data-blachon/SiteWeb/www/administration/'
				, 'compact' => true
				, 'force' => true
			);	
			public static $Static = array
			(
				'url' => ''//'http://s.Pole.fr'
			);			
		}
	}
?>