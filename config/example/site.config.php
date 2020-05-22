<?php
	//the default title for the public pages
	$arrConfig['SiteTitle'] = 'Phork Framework';
	
	//the theme to use for the site; themes must have their own templates
	$arrConfig['Theme'] = 'default';
	
	
	/*******************************************/
	/**     URLS                              **/
	/*******************************************/
	
	
	//the site urls
	$arrConfig['SiteUrl'] = 'http://' . $_SERVER['HTTP_HOST'];
	$arrConfig['ImageUrl'] = 'http://' . $_SERVER['HTTP_HOST'];
	$arrConfig['DocsUrl'] = 'http://docs.phorkit.org';
	$arrConfig['CssUrl'] = '';
	$arrConfig['JsUrl'] = '';
	
	//the demo urls
	$arrConfig['PhorkitUrl'] = 'http://archive.phorkit.org';
	$arrConfig['StandardUrl'] = 'http://standard.phorkit.org';
	$arrConfig['LiteUrl'] = 'http://lite.phorkit.org';
	
	//the url of the front controller (no trailing slash) excluding the filename if using mod rewrite
	$arrConfig['BaseUrl'] = '';
	
	//the domain to use for cookies
	$arrConfig['CookieDomain'] = $_SERVER['HTTP_HOST'];
	
	
	/*******************************************/
	/**     REQUEST VARS                      **/
	/*******************************************/
	
	
	//the names of various session and cookie vars
	$arrConfig['AlertSessionName'] = '_a';
	
	
	/*******************************************/
	/**     CSS & JS CONCAT                   **/
	/*******************************************/
	
	
	//the CSS and JS versions for cache busting
	$arrConfig['CssVersion'] = 1;
	$arrConfig['JsVersion'] = 1;
	
	//the domains that are trusted for CSS and JS files
	$arrConfig['AssetUrls'] = array(
		$arrConfig['CssUrl'],
		$arrConfig['JsUrl']
	);
	
	//the paths that are trusted for CSS and JS files
	$arrConfig['AssetPaths'] = array(
		AppConfig::get('SiteDir') . 'htdocs/css/',
		AppConfig::get('SiteDir') . 'htdocs/js/',
		AppConfig::get('SiteDir') . 'htdocs/lib/'
	);
	
	//whether to display the raw CSS and JS
	$arrConfig['NoConcat'] = true;
	
	
	/*******************************************/
	/**     PAGE CACHE                        **/
	/*******************************************/
	
	
	//define the url patterns for full page caches
	$arrConfig['CacheUrls'] = array(
		'#^/concat/(.*)#'	=> array(
			'Namespace'		=> null,
			'Expire'		=> 300,
			'Compress'		=> true
		)
	);
	
	
	/*******************************************/
	/**     ROUTING                           **/
	/*******************************************/
	
	
	//route the css and javascript
	$arrConfig['Routes']['^/concat/(css|js)/([0-9]*)/([^/]*)/[a-z]+.(css|js)$'] = '/concat/$1/version=$2/files=$3/';
	
	//route the index page to the manual page
	$arrConfig['Routes']['^/?$'] = '/site/redirect/status=301/manual/';
