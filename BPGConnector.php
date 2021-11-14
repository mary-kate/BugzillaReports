<?php
/**
 * PostgreSQL connector
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
class BPGConnector {
	protected $context;
	protected $error;

	/**
	 * @param BugzillaReports $context
	 */
	function __construct( $context ) {
		$this->setContext( $context );
	}

	/**
	 * @param BugzillaReports $context
	 */
	public function setContext( $context ) {
		$this->context = $context;
	}

	/**
	 * @return BugzillaReports
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * Attempt to estabilish a connection to the supplied PostgreSQL database with
	 * the given credentials (estabilished in the wiki's LocalSettings.php file)
	 *
	 * @return PgSql\Connection|false
	 */
	public function connect() {
		$db = pg_connect(
			'dbname=' . $this->context->database .
			' host=' . $this->context->host .
			' user=' . $this->context->dbuser .
			' password=' . $this->context->password
		);

		# $this->context->host, $this->context->dbuser, $this->context->password);

		if ( !$db ) {
			$this->setError(
				$this->context->getErrorMessage(
					'bReport_noconnection',
					$this->context->dbuser,
					$this->context->host,
					pg_last_error()
				)
			);
			return false;
		}

		if ( !pg_dbname( $db ) ) {
			$this->close( $db );
			$this->setError( $this->context->getErrorMessage( 'bReport_nodb' ) );
			return false;
		}

		return $db;
	}

	/**
	 * Run a SQL query against the database.
	 *
	 * @param string $sql SQL query string
	 * @param PgSql\Connection $db
	 * @return PgSql\Result|false
	 */
	public function execute( $sql, $db ) {
		return pg_query( $db, $sql );
	}

	/**
	 * Get the row count for the executed SQL query
	 *
	 * @param PgSql\Result $result
	 * @return int
	 */
	public function getRowCount( $result ) {
		return pg_num_rows( $result );
	}

	/**
	 * Fetch a row as an array
	 *
	 * @param PgSql\Result $result
	 * @return array|false
	 */
	public function fetch( $result ) {
		return pg_fetch_array( $result );
	}

	/**
	 * Free result memory
	 *
	 * @param PgSql\Result $result
	 */
	public function free( $result ) {
		pg_free_result( $result );
	}

	/**
	 * Close the database connection
	 *
	 * @param PgSql\Connection $db
	 */
	public function close( $db ) {
		pg_close( $db );
	}

	/**
	 * @param string $table
	 * @return string
	 */
	public function getTable( $table ) {
		return $table;
	}

	/**
	 * Set the class' error message string
	 *
	 * @param string $message
	 */
	public function setError( $message ) {
		$this->message = $message;
	}

	/** @return string */
	public function getError() {
		return $this->message;
	}

	/**
	 * Get the last database query error (if any) as a string
	 *
	 * @param PgSql\Connection|false $db
	 * @return string
	 */
	public function getDbError( $db ) {
		return pg_last_error( $db );
	}
}
