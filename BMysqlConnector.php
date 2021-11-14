<?php
/*
MySQL connector
*/

/**
 * Copyright (C) 2008 - Ian Homer & bemoko
 */
class BMysqlConnector {
	protected $context;
	protected $error;

	function __construct( $context ) {
		$this->setContext( $context );
	}

	public function setContext( $context ) {
		$this->context = $context;
	}

	public function getContext() {
		return $this->context;
	}

	public function connect() {
		$db = new mysqli();
		$db->real_connect( $this->context->host, $this->context->dbuser, $this->context->password, null, null, null, MYSQLI_CLIENT_SSL );

		/*
		 * Set character encoding
		 */
		$db->query( "SET NAMES '" . $this->context->dbencoding . "';" );
		$db->query( "SET CHARACTER SET '" . $this->context->dbencoding . "';" );

		if ( $db->connect_errno ) {
			$this->setError(
				$this->context->getErrorMessage(
					'bReport_noconnection',
					$this->context->dbuser,
					$this->context->host,
					$db->connect_error
				)
			);
			return false;
		}

		/*
		 * Test the connection early - note that we can't switch to the db
		 * with select_db since if this is a shared database connection
		 * with mediawiki then we will have changed the db for the mediawiki
		 * access.
		 */
		$sql = "select count(id) from `" . $this->context->database . "`" .
			".priority;";
		$result = $db->query( $sql );

		if ( !$result ) {
			$this->setError( $this->context->getErrorMessage( 'bReport_nodb',
				"Can't find test table 'priority' in database ".
				"`" . $this->context->database . "` using " . $sql .
				" - this probably means your username and password set in the variable wgBugzillaReports are not correct."
			) );
			$db = null;
		} elseif ( $db->error ) {
			$this->setError( $this->context->getErrorMessage( 'bReport_nodb' ), $db->error );
			$db = null;
		} elseif ( $this->getRowCount( $result ) != 1 ) {
			$this->setError( $this->context->getErrorMessage(
				'bReport_nodb',
				"`" . $this->context->database . "`-" . $this->getRowCount( $result )
			) );
			$db = null;
		}
		$this->free( $result );

		return $db;
	}

	public function execute( $sql, $db ) {
		return $db->query( $sql );
	}

	public function getRowCount( $result ) {
		return $result->num_rows;
	}

	public function fetch( $result ) {
		return $result->fetch_assoc();
	}

	public function free( $result ) {
		$result->close();
	}

	public function close( $db ) {
		/*
		 * In PHP you should rely on script termination to close mysql
		 * and not explicitly call close() - see
		 * http://uk.php.net/manual/en/function.mysql-close.php
		 * This is because the implementation may reuse connections.
		 * This does happen if the connection details for the Bugzila database are
		 * the same as the wiki database.
		 * Setting to null is good practice to free up the resource early.
		 */
		$db = null;
	}

	public function getTable( $table ) {
		return '`' . $this->context->database . '`.' . $table;
	}

	public function setError( $message ) {
		$this->message = $message;
	}

	public function getError() {
		return $this->message;
	}

	public function getDbError( $db ) {
		return $db->error;
	}

}
