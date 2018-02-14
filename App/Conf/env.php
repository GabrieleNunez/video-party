<?php
$env = array(
	'debug' => true,
	'domain' => 'lounge.levelcrush.com',
	'owner' =>  array(
		'name' => 'Level Crush',
		'email' => 'g_m_nunez@live.com', // for now this will simply be Gabriele Nunez's email ( the maintainer ) 
		'phone' => '',
		'phone_formatted' => '',
		'facebook' => 'https://www.facebook.com/levelcrushgames/',
		'twitter' => 'https://www.twitter.com/LevelCrush',
	),
	'viewcache' => '../tmp/views',
	'bronco_cache' => true,
	'mailer' => array(
		'smtp' => false,
		'username' => '',
		'password' => '',
		'host' => '',
		'protocol' => 'tls',
		'port' => '',
		'from' => 'no-reply@levelcrush.com',
		'name' => 'Level Crush',
		'html' => true
	),
	'folders' => array(
		'views' => '../App/Views',
		'public' => '../public_html/',
		'migration' => '../Migration/',zs
	),
	'files' => array(
		'routes' => '../App/Conf/routes.php',
		'database' => '../App/Conf/database.php'
	),
	'vendor' => array(
		'Mustache' => '../vendor/Mustache/'
	),
    'memcached' => array(
        'host' => 'localhost',
        'port' => 11211
    ),
    'recaptcha' => array(
    	'key_public' => '',
    	'key_secret' => '',
    ),
);
?>