<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(

	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'youtube9loop',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),
	
	'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'abcd1234',
            'ipFilters'=>array('***only-your-ip***')
        ),
    ),

	'defaultController'=>'site',

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=youtube9loop',
			'emulatePrepare' => true,
			'username' => '***my-sql-username***',
			'password' => '***my-sql-password***',
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			//'errorAction'=>'site/error',
		),
		'urlManager'=>array(
		    'urlFormat'=>'path'      
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				array(
					'class'=>'CWebLogRoute',
				),
			),
		),
    'geoIp2' => array(
      'class' => 'GeoIP2Component',
      'cityMmdb' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mmdb' . DIRECTORY_SEPARATOR . 'GeoLite2-City.mmdb'
    ),
    'facebookThumbnail' => array(
      'class' => 'FacebookThumbnailComponent'
    ),
    'youtube' => array(
      'class' => 'YoutubeComponent',
      'youtubeApiKey' => '***youtube-api-key***'
    )
  ),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);