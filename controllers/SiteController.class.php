<?php
	require_once('php/core/CoreController.class.php');
	
	/**
	 * SiteController.class.php
	 * 
	 * This is the base controller for the public site which
	 * consists of several different separately cacheable
	 * nodes defined in the $arrNodeOrder property.
	 *
	 * In addition to the regular templates there are themed
	 * templates. Themed templates are used to override the
	 * standard templates if a design calls for a different look.
	 * 
	 * Copyright 2006-2011, Phork Labs. (http://phorklabs.com)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @author Elenor Collings <elenor@phork.org>
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @package phorkit
	 * @subpackage controllers
	 */
	class SiteController extends CoreController {
		
		protected $strThemeDir;
		protected $strThemeCssDir;
		protected $strThemeJsDir;
		
		protected $arrNodeOrder = array('header', 'nav', 'errors', 'content', 'footer');
				
		
		/**
		 * Sets up the common page variables to be used
		 * across all node templates.
		 * 
		 * @access public
		 */
		public function __construct() {
			AppConfig::get('NodeCacheEnabled', false) || $this->setNoCache(true);
			parent::__construct();
			
			$this->assignPageVar('strBaseUrl', AppConfig::get('BaseUrl'));
			$this->assignPageVar('strDocsUrl', AppConfig::get('DocsUrl'));
			$this->assignPageVar('strPageTitle', $strSiteTitle = AppConfig::get('SiteTitle'));
			$this->assignPageVar('strSiteTitle', $strSiteTitle);
			$this->assignPageVar('strTheme', $strTheme = AppConfig::get('Theme'));
			
			$this->strThemeDir = ($strTheme ? "themes/{$strTheme}/" : '');
			$this->strThemeCssDir = '/css/' . $this->strThemeDir;
			$this->strThemeJsDir = '/js/' . $this->strThemeDir;
		}
		
				
		/**
		 * Returns the template path for the page templates.
		 * If a theme has an overriding template that path is
		 * returned, otherwise it returns the common path.
		 *
		 * @access protected
		 * @param string $strTemplate The name of the template
		 * @return string The path to the template
		 */
		protected function getTemplatePath($strTemplate) {
			if ($this->strThemeDir && file_exists($strThemeTemplateDir = $this->strTemplateDir . $this->strThemeDir . $strTemplate . '.phtml')) {
				return $strThemeTemplateDir;
			} else {
				return $this->strTemplateDir . $strTemplate . '.phtml';
			}
		}
		
		
		/*****************************************/
		/**     INCLUDE METHODS                 **/
		/*****************************************/
		
		
		/**
		 * Includes a common template that doesn't warrant its
		 * own specific include method.
		 *
		 * @access public
		 * @param string $strFile The template file relative to the common dir
		 * @param array $arrPageVars The variables to pass on to the template
		 */
		public function includeCommon($strFile, $arrPageVars = array()) {
			$this->includeTemplateFile($this->getTemplatePath('common/' . $strFile), $arrPageVars);
		}
		
		
		/*****************************************/
		/**     DISPLAY METHODS                 **/
		/*****************************************/
		
		
		/**
		 * Displays the navigation template.
		 *
		 * @access protected
		 */
		protected function displayNav() {
			$this->displayNode('nav', $this->getTemplatePath('common/nav'));
		}
		
		
		/**
		 * Displays the index page.
		 *
		 * @access protected
		 */
		protected function displayIndex() {
			$this->displayNode('content', $strTemplatePath = $this->getTemplatePath('index'));
		}
		
		
		/**
		 * Permanently redirects the user to a new location
		 * determined by the routed URL. The route should be
		 * in the format /site/redirect/[controller]/[method]/status=301/
		 * where method defaults to index if it's left out
		 * and the status is optional.
		 *
		 * @access protected
		 */
		protected function displayRedirect() {
			$objUrl = AppRegistry::get('Url');
			if ($arrSegments = array_slice($objUrl->getSegments(), 2)) {
				$strLocation = implode('/', $arrSegments) . '/';
			}
			
			if (isset($strLocation)) {
				$objDisplay = AppDisplay::getInstance();
				if ($objUrl->getFilter('status') == 301) {
					$objDisplay->setStatusCode(301);
				}
				$objDisplay->appendHeader('Location: ' . AppConfig::get('BaseUrl') . '/' . $strLocation);
			} else {
				$this->error(404);
			}
		}
	}