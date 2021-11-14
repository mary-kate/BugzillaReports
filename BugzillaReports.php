<?php
/**
 * The bugzilla report objects
 */

/**
 * Copyright (C) 2008 - Ian Homer & bemoko
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses>.
 */

class BugzillaReports extends BMWExtension {

	/** @var BugzillaQuery The handle on the query object */
	public $query;

	/** @var int Default max rows for a report from configuration */
	public $maxrowsFromConfig;

	/** @var int Default max rows for a report */
	public $maxrowsFromConfigDefault = 100;

	/** @var string String describing which database driver to use */
	public $dbdriverDefault = 'mysql';

	/** @var int Default max rows which are used for aggregation of a bar chart report */
	public $maxrowsForBarChartFromConfig;
	public $maxrowsForBarChartFromConfigDefault = 500;

	/** @var bool Output raw HTML (i.e. not wikitext)? */
	public $rawHTML;

	/** @var string Bugzilla database username */
	public $dbuser;

	/** @var string Bugzilla database server name or IP address */
	public $bzserver;

	public $interwiki;
	public $database, $host, $password;
	public $dbdriver;
	public $dbencoding;
	public $instanceNameSpace;

	/**
	 * @param &$parser Parser
	 */
	function __construct( &$parser ) {
		$this->parser =& $parser;
	}

	/**
	 * Register the function hook
	 */
	public static function parserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'bugzilla', 'BugzillaReports::parserHook' );
		$parser->setFunctionHook( 'bugzilla', 'BugzillaReports::parserFunctionHook' );
	}

	/**
	 * Call to render the bugzilla report
	 */
	public static function parserHook( $input, array $args, Parser $parser, PPFrame $frame ) {
		$parserArgs = [ &$parser ];
		foreach ( $args as $k => $v ) {
			$parserArgs[] = $k . '=' . $v;
		}
		return call_user_func_array( 'BugzillaReports::parserFunctionHook', $parserArgs );
	}

	/**
	 * Callback for the {{#bugzilla:}} parser function
	 *
	 * @param &$parser Parser
	 * @return array
	 */
	public static function parserFunctionHook( Parser &$parser ) {
		$args = func_get_args();
		array_shift( $args );
		$bugzillaReport = new BugzillaReports( $parser );
		return [
			$parser->recursiveTagParse( $bugzillaReport->render( $args ) ),
			'noparse' => true,
			'isHTML' => true
		];
	}

	/**
	 * @var array $args User-supplied arguments
	 * @return string
	 */
	public function render( $args ) {
		global $wgBugzillaReports;
		global $wgDBserver, $wgDBname, $wgDBuser, $wgDBpassword;

		// Initialise query
		$this->dbdriver = $this->getProperty( 'dbdriver', $this->dbdriverDefault );
		$connector;
		switch ( $this->dbdriver ) {
			case 'pg':
				$connector = new BPGConnector( $this );
				break;
			default:
				$connector = new BMysqlConnector( $this );
		}

		$this->query = new BugzillaQuery( $connector );

		// Process arguments from default setting across all the wiki
		$this->extractOptions( explode( '|', $this->getProperty( 'default' ) ) );

		// Process arguments for this particular query
		$this->extractOptions( $args );

		if ( $this->query->get( 'instance' ) != null ) {
			$this->instanceNameSpace = $this->query->get( 'instance' );
		}

		// Allow the user to specify alternate DB connection info by name
		// in their query.
		if ( $this->query->get( 'bzalternateconfig' ) != null ) {
			// The user has asked for an alternate BZ iestall to be queried.
			$alternateConfigName = $this->query->get( 'bzalternateconfig' );
			$bzAlternateConfigs = $this->getProperty( 'bzAlternateConfigs' );
			if ( is_array( $bzAlternateConfigs[$alternateConfigName] ) ) {
				// We appear to have an array...set values.
				$this->dbuser = $bzAlternateConfigs[$alternateConfigName]['user'];
				$this->bzserver = $bzAlternateConfigs[$alternateConfigName]['bzserver'];
				$this->database = $bzAlternateConfigs[$alternateConfigName]['database'];
				$this->host = $bzAlternateConfigs[$alternateConfigName]['host'];
				$this->password = $bzAlternateConfigs[$alternateConfigName]['password'];
			}
		} else {
			// Use the defaults from LocalSettings.php.
			$this->dbuser = $this->getProperty( 'user', $wgDBuser );
			$this->bzserver = $this->getProperty( 'bzserver', null );
			$this->database = $this->getProperty( 'database' );
			$this->host = $this->getProperty( 'host' );
			$this->password = $this->getProperty( 'password' );
		}

		$this->interwiki = $this->getProperty( 'interwiki', null );
		$this->dbencoding = $this->getProperty( 'dbencoding', 'utf8' );
		$this->maxrowsFromConfig = $this->getProperty( 'maxrows', $this->maxrowsFromConfigDefault );
		$this->maxrowsForBarChartFromConfig = $this->getProperty( 'maxrowsbar', $this->maxrowsForBarChartFromConfigDefault );
		if ( $this->query->get( 'disablecache' ) != null ) {
			// Extension parameter take priority on disable cache configuration
			if ( $this->query->get( 'disablecache' ) == '1' ) {
				$this->disableCache();
			}
		} elseif ( $this->getProperty( 'disablecache' ) == '1' ) {
			// ... then it's the LocalSettings property
			$this->disableCache();
		}

		/**
		 * Add CSS and JavaScript to output
		 */
		$this->parser->getOutput()->addModules( 'ext.bugzillareports' );

		$this->debug && $this->debug( 'Rendering BugzillaReports' );

		return $this->query->render() . $this->getWarnings();
	}

	/**
	 * Disable parser caching
	 */
	protected function disableCache() {
		$this->debug && $this->debug( 'Disabling parser cache for this page' );
		$this->parser->updateCacheExpiry( 0 );
	}

	/**
	 * Set value - implementation of the abstract function from BMWExtension
	 *
	 * @param string $name
	 * @param string|int|null $value
	 */
	protected function set( $name, $value ) {
		// debug variable is store on this object
		if ( $name == 'debug' ) {
			$this->$name = $value;
		} else {
			$this->query->set( $name, $value );
		}
	}

	/**
	 * @param string $name
	 * @return string Regex
	 */
	protected function getParameterRegex( $name ) {
		if ( $name == 'debug' ) {
			return "/^[12]$/";
		} else {
			return $this->query->getParameterRegex( $name );
		}
	}

	/**
	 * Read a value from $wgBugzillaReports or return $default if no such array key is set
	 *
	 * @param string $name
	 * @param string $default Default value, if any
	 */
	function getProperty( $name, $default = '' ) {
		global $wgBugzillaReports;

		$value;
		if (
			$this->instanceNameSpace != null &&
			array_key_exists( $this->instanceNameSpace . ':' . $name, $wgBugzillaReports )
		) {
			$value = $wgBugzillaReports[$this->instanceNameSpace . ':' . $name];
		} elseif ( array_key_exists( $name, $wgBugzillaReports ) ) {
			$value = $wgBugzillaReports[$name];
		} else {
			$value = $default;
		}

		$this->debug && $this->debug( "Env property $name=$value" );

		return $value;
	}

	/**
	 * @param string $key i18n message key
	 * @return string Output suitable for both wikitext and HTML
	 */
	public function getErrorMessage( $key ) {
		$args = func_get_args();
		array_shift( $args );
		return '<strong class="error">BugzillaReports : ' . wfMessage( $key, $args )->inContentLanguage()->text() . '</strong>';
	}

	/**
	 * Enable outputting of raw HTML instead of wikitext?
	 *
	 * @param bool $bool
	 */
	public function setRawHTML( $bool ) {
		$this->rawHTML = $bool;
	}

}

