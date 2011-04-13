<?php
	require_once('SiteController.class.php');
	
	/**
	 * ManualController.class.php
	 * 
	 * This controller handles the manual. It uses custom
	 * templates from the manual subdirectory.
	 * 
	 * Copyright 2006-2011, Phork Labs. (http://phorklabs.com)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @author Elenor Collings <elenor@phork.org>
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 * @package phork
	 * @subpackage controllers
	 */
	class ManualController extends SiteController {
		
		protected $strManualDir = 'manual';
		protected $strManualTemplate;
		
		protected $arrNodeOrder = array('header', 'nav', 'errors', 'alerts', 'title', 'content', 'footer');
		protected $arrToc;
		
		
		/**
		 * Sets up the common page variables to be used
		 * across all node templates, including the styles
		 * and javascript.
		 * 
		 * @access public
		 */
		public function __construct() {
			parent::__construct();		
		
			$this->assignPageVar('arrStylesheets', array(
				AppConfig::get('CssUrl') . $this->strThemeCssDir . 'manual.css'
			));
			$this->assignPageVar('arrJavascript', array(
				AppConfig::get('JsUrl') . $this->strThemeJsDir . 'manual.js'
			));
		}
		
		
		/**
		 * Determines which page to display and displays it.
		 * If there are URL parts it builds the page from that.
		 *
		 * @access public
		 */
		public function run() {
			$this->strContent = 'Manual';
			
			$arrUrlSegments = array_slice(AppRegistry::get('Url')->getSegments(), 1);
			if (!empty($arrUrlSegments)) {
				$this->strManualTemplate = $this->getTemplatePath($this->strManualDir . '/' . ($strPage = str_replace(':', '_', strtolower(implode('/', $arrUrlSegments)))));
			} else {
				$this->strManualTemplate = $this->getTemplatePath($this->strManualDir . '/' . ($strPage = 'index'));
			}
			
			$this->assignPageVar('strSubTitle', $strSubTitle = !empty($arrUrlSegments) ? implode(' / ', array_map(array($this, 'formatTitleSegment'), $arrUrlSegments)) : '');
			$this->assignPageVar('strPageTitle', AppConfig::get('SiteTitle') . ' - Manual' . ($strSubTitle ? ' / ' . $strSubTitle : ''));
			$this->assignPageVar('blnFooterToc', $strPage != 'contents');
			$this->assignPageVar('strBodyClass', trim(($arrUrlSegments ? implode('-', $arrUrlSegments) : 'intro') . ($this->getPageVar('blnFooterToc') ? ' has-toc' : '')));
			$this->assignPageVar('strPhorkitUrl', AppConfig::get('PhorkitUrl'));
			$this->display();
		}
		
		
		/**
		 * Returns the URL segment formatted for the page title.
		 * Called by array_map in run().
		 *
		 * @access protected
		 * @param string $strTitle The page title segment
		 * @return string THe formatted title based on the segment
		 */
		protected function formatTitleSegment($strTitle) {
			$arrTitleMap = array(
				'info'		=> 'Basic Info',
				'install'	=> 'Installation',
				'start'		=> 'Getting Started',
				'overview'	=> 'Framework Overview',
				'core'		=> 'Core Classes',
				'database'	=> 'Database Classes',
				'cache'		=> 'Cache Classes',
				'hooks'		=> 'Hook Classes',
				'utility'	=> 'Utility Classes',
				'debug'		=> 'Debug Extension',
				'zend'		=> 'Zend Extension',
				'filesys'	=> 'Filesystem Extension'
			);
			return (!empty($arrTitleMap[$strTitle]) ? $arrTitleMap[$strTitle] : ucwords($strTitle));
		}
		
		
		/*****************************************/
		/**     INCLUDE METHODS                 **/
		/*****************************************/
		
		
		/**
		 * Includes the table of contents template.
		 *
		 * @access public
		 */
		public function includeTableOfContents() {
			$this->includeTemplateFile($this->getTemplatePath('manual/common/contents'));
		}
		
		
		/*****************************************/
		/**     DISPLAY METHODS                 **/
		/*****************************************/
		
		
		/**
		 * Displays the manual title with links to the previous
		 * and next chapter.
		 *
		 * @access protected
		 */
		protected function displayTitle() {
			$this->displayNode('title', $this->getTemplatePath('manual/title'), array(
				'arrPrevChapter' => $this->getPrevChapter(),
				'arrNextChapter' => $this->getNextChapter()
			));
		}


		/**
		 * Displays the manual content. If an empty placeholder
		 * file exists this will display a coming soon alert.
		 *
		 * @access protected
		 */
		protected function displayManual() { 
			if (file_exists($this->strManualTemplate)) {
				if (filesize($this->strManualTemplate) === 0) {
					$this->displayNode('content', $this->getTemplatePath('manual/common/soon'));
				} else {
					CoreLoader::includeUtility('Markup');
					$this->displayNode('content', $this->strManualTemplate);
				}
			} else {
				$this->error(404);
			}
		}
		
		
		/*****************************************/
		/**     NAVIGATION METHODS              **/
		/*****************************************/
		
		
		/**
		 * Returns the URL and title of the next chapter in the
		 * manual by parsing the table of contents for links and
		 * getting the link after the current page.
		 *
		 * @access protected
		 * @return array The URL and title of the next chapter
		 */
		protected function getNextChapter() {
			if ($arrToc = $this->parseTableOfContents()) {
				$arrUrls = array_keys($arrToc);
				$arrTitles = array_values($arrToc);
				
				if (($intKey = array_search(AppRegistry::get('Url')->getUrl(), $arrUrls)) !== false) {
					if (!empty($arrUrls[++$intKey])) {
						return array(
							'Url'	=> $arrUrls[$intKey],
							'Title'	=> $arrTitles[$intKey]
						);
					}
				}
			}
		}
		
		
		/**
		 * Returns the URL and title of the previous chapter in the
		 * manual by parsing the table of contents for links and
		 * getting the link before the current page.
		 *
		 * @access protected
		 * @return array The URL and title of the previous chapter
		 */
		protected function getPrevChapter() {
			if ($arrToc = $this->parseTableOfContents()) {
				$arrUrls = array_keys($arrToc);
				$arrTitles = array_values($arrToc);
				
				if ($intKey = array_search(AppRegistry::get('Url')->getUrl(), $arrUrls)) {
					if (!empty($arrUrls[--$intKey])) {
						return array(
							'Url'	=> $arrUrls[$intKey],
							'Title'	=> $arrTitles[$intKey]
						);
					}
				}
			}
		}
		
		
		/**
		 * Parses the URLs and their corresponding titles from the
		 * table of contents file and returns the resulting array.
		 *
		 * @access protected
		 * @return array The parsed contents file
		 */
		protected function parseTableOfContents() {
			if (!$this->arrToc) {
				if ($strToc = file_get_contents($this->getTemplatePath('manual/common/contents'))) {
					if (preg_match_all('#<a href=".*(/manual/[^"]*)[^>]*">(.*)</a>#', $strToc, $arrMatches)) {
						$this->arrToc = array_combine($arrMatches[1], $arrMatches[2]);
					}
				}
			}
			return $this->arrToc;
		}
	}