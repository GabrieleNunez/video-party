<?php
$env = array(
	'debug' => true,
	'domain' => 'yourdomainhere',
	'owner' =>  array(
		'name' => 'NameHere',
		'email' => 'emailhere',
		'phone' => '',
		'phone_formatted' => '',
		'facebook' => 'social-link-facebook-here',
		'twitter' => 'social-link-twitter-here',
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
		'from' => 'no-reply@domain.com',
		'name' => 'NameHere',
		'html' => true
	),
	'folders' => array(
		'views' => '../App/Views',
		'public' => '../public_html/',
		'migration' => '../Migration/',
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