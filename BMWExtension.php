<?php
/**
 * Bemoko MediaWiki Extension
 */

/**
 * Copyright (C) 2008 - Ian Homer & bemoko
 */
abstract class BMWExtension {
	public $debug;
	public $warnings;

	abstract protected function set( $name, $value );

	/**
	 * Extract options from a arguments
	 *
	 * @param string $args function arguments
	 */
	public function extractOptions( $args ) {
		foreach ( $args	as $line ) {
			if ( strpos( $line, '=' ) === false ) {
				continue;
			}

			list( $name, $value ) = explode( '=', $line, 2 );
			$value = trim( $value );
			$match = $this->getParameterRegex( $name );
			if ( !$match ) {
				/**
				 * Safe parameter reading by default
				 * only allowing alphanumeric
				 */
				$match = "/^[\w]*$/";
			}

			if ( preg_match( $match, $value, $matches ) ) {
				$this->set( $name, $matches[0] );
				$this->debug && $this->debug( "Parameter [{$name}={$matches[0]}]" );
			} else {
				$this->warn( "Parameter {$name}={$value} is invalid using regex $match" );
			}
		}

		$this->debug && $this->debug( 'Called extractOptions : ' . $args );
	}

	/**
	 * MediaWiki debug message
	 */
	public function debug( $message ) {
		if ( $this->debug ) {
			if ( $message ) {
				wfDebugLog( 'BMWExtension', $message );
			} else {
				$this->warn( 'Null message sent to debug statement' );
			}
		}
	}

	/**
	 * MediaWiki warn message
	 */
	public function warn( $message ) {
		wfDebugLog( 'BMWExtension', 'WARN: *** ' . $message );
		$this->warnings .= '<li>' . $message . '</li>';
	}

	public function getWarnings() {
		if ( $this->warnings ) {
			return '<div class="warning"><b>Warnings were generated during the execution of function</b><ol>' . $this->warnings . '</ol></div>';
		} else {
			return null;
		}
	}

}
