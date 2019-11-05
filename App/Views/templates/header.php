<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<meta charset="utf-8" />
    	<meta http-equiv="x-ua-compatible" content="ie=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

    	<!-- favicon code -->
    	<link rel="shortcut icon"  href="/favicon.png" type="image/x-icon" />
    	<link rel="apple-touch-icon-precomposed" href="/favicon.png" />

    	<!-- social media metadata code -->

		<!-- style sheets -->
		<link href="/css/foundation.min.css" type="text/css" rel="stylesheet" type="text/css" />
		<link href="/css/foundation-icons/foundation-icons.css" rel="stylesheet" type="text/css" />
		<link href="http://vjs.zencdn.net/6.6.3/video-js.css" rel="stylesheet">
		<link href="/css/app_beta.css" type="text/css" rel="stylesheet" />
		
		<?php if ($maintab == 'manager') { ?>
			<link href="/css/manager.css" type="text/css" rel="stylesheet" rev="stylesheet" />
		<?php } ?>
		<script src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>

	</head>
	<body>
		<!-- version - <?php echo \Library\Application::setting('debug') ? 'development' : 'production'; ?> -->
		<div id="wrapper">