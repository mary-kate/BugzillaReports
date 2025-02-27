<?php
/**
 * A bugzilla query
 */

/**
 * Copyright (C) 2008 - Ian Homer & bemoko
 */
class BugzillaQuery extends BSQLQuery {
	public $supportedParameters = [
		'alias'         => 'field-id',
		'assigned'      => 'field-date',
		'attachments'   => 'field-number',
		'bar'           => 'column',
		'bzalternateconfig' => 'free',
		'bzurl'         => 'value',     # Show the url to the BZ query
		'blocks'        => 'field-depends',
		'cc'            => 'field',
		'closed'        => 'field-date',
		'columns'       => 'columns',
		'created'       => 'field-date',
		'createdformat' => 'value',   # date or relativedate
		'component'     => 'field',
		'customfields'  => 'value',     # Comma separated list of custom fields
		'customprefix'  => 'cf_',     # Prefix that all custom fields should start with
		'deadline'      => 'field-date',
		'depends'       => 'field-depends',
		'dependsstatus' => 'field-join',  # Status values to include in depends (and blocks) tasks
		'detailsrow'    => 'columns',
		'detailsrowprepend' => 'free',
		'disablecache'    => 'boolean',
		'estimated'     => 'field-number',
		'filters'       => 'filters',   # Generic filter setting which can be used for custom fields
		'flag'          => 'field-special',
		'format'        => 'value',     # table (default), list, inline or count
		'from'          => 'field',
		'group'         => 'sort',
		'groupformat'   => 'value',
		'grouporder'    => 'value',     # asc or desc
		'hardware'      => 'field',
		'heading'       => 'free',
		'headers'       => 'value',
		'hide'          => 'value',
		'id'            => 'field-id',
		'implicitcustom'  => 'boolean',   # true => allow custom fields not explicitly defined and not
                        # starting with the custom field prefix
		'instance'      => 'value',     # Alternative bugzilla instance as defined in
                        # LocalSettings configuration
		'keywords'      => 'field-keywords',
		'link'          => 'columns',   # Define rules for linking headings and values
                        # through to wiki pages
		'lastcomment'   => 'boolean',
		'maxrows'       => 'value',
		'maxrowsbar'    => 'value',
		'milestone'     => 'field',
		'modified'      => 'field-date',
		'modifiedformat'  => 'value',   # date or relativedate
		'nameformat'    => 'value',     # real (default),tla or login
		'order'         => 'value',
		'os'            => 'field',
		'priority'      => 'field',
		'product'       => 'field',
		'qa'            => 'field',
		'quickflag'     => 'value',
		'noresultsmessage'  => 'free',
		'remaining'     => 'field-number',
		'reopened'      => 'field-date',
		'resolution'    => 'field',
		'resolved'      => 'field-date',
		'search'        => 'field-text',
		'severity'      => 'field',
		'sort'          => 'sort',
		'sortable'      => 'boolean',   # Whether the table is sortable or not
		'status'        => 'field',
		'status_whiteboard' => 'field',
		'style'         => 'free',      # Define the CSS style to be applied to the table
		'to'            => 'field',
		'total'         => 'columns',
		'url'           => 'field',
		'version'       => 'field',
		'verified'      => 'field-date',
		'votes'         => 'field-number',
		'work'          => 'field-number',
		'zeroasblank'   => 'boolean'    # Render '0' as blank, if false rendered as '0' (default=true)
	];

	public $defaultParameters = [
		'bzurl'           => 'show',
		'columns'         => 'id,priority,status,severity,version,product,summary,url',
		'customprefix'    => 'cf_',
		'dependsstatus'   => '!(CLOSED,VERIFIED,RESOLVED)',
		'format'          => 'table',
		'implicitcustom'  => 'false',     # Default to false since Bugzilla nowadays enforces custom
                                          # fields to start with 'cf_'
		'noresultsmessage'  => 'no bugzilla tickets were found',
		'order'           => 'asc',
		'status'          => '!CLOSED',
		'sort'            => 'priority,status',
		'sortable'        => '1',
		'zeroasblank'     => 'true'
	];

	public $columnName = [
		'alias'       => 'Alias',
		'assigned'    => 'Assigned',
		'attachments' => '@',
		'blocks'      => 'Blocks',
		'closed'      => 'Closed',
		'component'   => 'Component',
		'cc'          => 'CC',
		'created'     => 'Created',
		'deadline'    => 'Deadline',
		'depends'     => 'Depends',
		'estimated'   => 'E',
		'flag'        => 'Flagged For',
		'flagdate'    => 'Flag Date',
		'flagfrom'    => 'Flagged By',
		'flagname'    => 'Flag',
		'from'        => 'Requester',
		'hardware'    => 'Hardware',
		'keyworddefs.name'    => 'Keywords',
		'id'          => 'ID',
		'milestone'   => 'Milestone',
		'modified'    => 'Modified',
		'os'          => 'OS',
		'product'     => 'Product',
		'priority'    => 'P',
		'qa'          => 'QA',
		'remaining'   => 'R' ,
		'reopened'    => 'Reopened',
		'resolution'  => 'Resolution',
		'resolved'    => 'Resolved',
		'severity'    => 'Severity',
		'status'      => 'Status',
		'summary'     => 'Summary',
		'to'          => 'Assignee',
		'url'         => '&nbsp;',
		'version'     => 'Version',
		'verified'    => 'Verified',
		'votes'       => 'Votes',
		'work'        => 'W'
	];

	public $columnLabelName = [
		'estimated'   => 'Estimated',
		'priority'    => 'Priority',
		'remaining'   => 'Remaining',
		'work'        => 'Work'
	];

	/** @var array Fields and their mapping to the value in the results sets */
	public $fieldMapping = [
		'cc'          => 'cc',
		'from'        => 'raisedby',
		'to'          => 'assignedto',
	];

	public $fieldSQLColumn = [
		'assigned'    => 'assignedactivity.bug_when',
		'attachments' => 'attachments.nattachments',
		'cc'          => 'ccprofiles.login_name',
		'component'   => 'components.name',
		'closed'      => 'closedactivity.bug_when',
		'created'     => 'creation_ts',
		'estimated'   => 'estimated_time',
		'hardware'    => 'rep_platform',
		'id'          => 'bugs.bug_id',
		'from'        => 'reporterprofiles.login_name',
		'keywords'    => 'keyworddefs.name',
		'milestone'   => 'target_milestone',
		'modified'    => 'lastdiffed',
		'product'     => 'products.name',
		'os'          => 'op_sys',
		'qa'          => 'qaprofiles.login_name',
		'remaining'   => 'remaining_time',
		'reopened'    => 'reopenedactivity.bug_when',
		'resolved'    => 'resolvedactivity.bug_when',
		'severity'    => 'bug_severity',
		'status'      => 'bug_status',
		'to'          => 'profiles.login_name',
		'url'         => 'bug_file_loc',
		'verified'    => 'verifiedactivity.bug_when',
		'work'        => 'work_time'
	];

	/** @var array Bugzilla Query field names */
	public $fieldBZQuery = [
		'blocks'      => 'blocked',
		'hardware'    => 'rep_platform',
		'id'          => 'bug_id',
		'milestone'   => 'target_milestone',
		'os'          => 'op_sys',
		'qa'          => 'qa_contact',
		'severity'    => 'bug_severity',
		'status'      => 'bug_status',
		'to'          => 'assigned_to'
	];

	public $fieldDefaultOrder = [
		'modified'    => 'desc',
		'votes'       => 'desc'
	];

	public $formats = [
		'alias'     => 'id',
		'assigned'  => 'date',
		'blocks'    => 'id',
		'cc'        => 'name',
		'created'   => 'date',
		'closed'    => 'date',
		'deadline'  => 'date',
		'depends'   => 'id',
		'estimated' => 'number',
		'flagdate'  => 'date',
		'flagfrom'  => 'name',
		'from'      => 'name',
		'id'        => 'id',
		'modified'  => 'relativedate',
		'qa'        => 'name',
		'remaining' => 'number',
		'reopened'  => 'date',
		'resolved'  => 'date',
		'to'        => 'name',
		'url'       => 'url',
		'votes'     => 'number',
		'work'      => 'number'
	];

	public $fieldValues = [
		'priority'    => 'P1,P2,P3,P4,P5',
		'status'      => 'ASSIGNED,NEW,REOPENED,RESOLVED,VERIFIED,CLOSED',
		'severity'    => 'blocker,critical,major,normal,minor,trivial,enhancement'
	];

	public $sortMapping = [
		'deadline'    => "COALESCE(deadline, '2100-01-01')",
		'milestone'   => "COALESCE(NULLIF(milestone,'---'),'XXXXX')",
		'id'          => 'bugs.bug_id'
	];

	public $dependsRowColumns = [
		'depends'         => 'block',
		'dependsto'       => 'title',   # Output in the title
		'dependsstatus'   => 'extra',   # Output as greyed
		'dependssummary'  => 'block',
	];

	public $blocksRowColumns = [
		'blocks'          => 'block',
		'blocksto'        => 'title',   # Output in the title
		'blocksstatus'    => 'extra',
		'blockssummary'   => 'block'
	];

	/** @var array Title for a given value rendering */
	public $valueTitle = [
		'alias'       => 'id,alias',
		'blocks'      => 'blocks,blocksalias',
		'depends'     => 'depends,dependsalias',
		'id'          => 'id,alias'
	];

	private $supportedCustomFields = [];	# Supported custom fields
	private $requiredCustomFields = [];	# Custom fields that are required for the report
	private $bzFieldCount = 0;					# Field counter for BZ URL
	private $customPrefixLength;				# Cached length of custom prefix length

	public $bzURL = '';				 # Bugzilla URL to run query
	public $explitlyOneValue; # Set if report is explictly one value

	public static $fieldIds = [];
	public static $buglistServerRelativeUri = '/buglist.cgi?';

	/**
	 * Parse in a context object which implements the following
	 *
	 * Public Variables
	 * - debug, bzserver, interwiki,
	 * - database, host, dbuser, password;
	 *
	 * Functions
	 * - debug
	 * - warn,
	 * - getErrorMessage
	 *
	 * @param BPGConnector|BMysqlConnector $connector
	 */
	function __construct( $connector ) {
		$this->setConnector( $connector );
		$this->setContext( $connector->getContext() );
	}

	/**
	 * Get rendering formats
	 *
	 * @return array
	 */
	function getFormats() {
		return $this->formats;
	}

	/**
	 * Get default priority
	 *
	 * @return string
	 */
	function getDefaultSort() {
		return $this->defaultSort;
	}

	/**
	 * Render the results
	 *
	 * @return string HTML
	 */
	function render() {
		$this->bzURL = $this->context->bzserver . BugzillaQuery::$buglistServerRelativeUri;

		// Register supported custom fields
		if ( $this->get( 'customfields' ) ) {
			$this->supportedCustomFields = explode( ',', $this->get( 'customfields' ) );
		}

		// Calculate the customPrefixLength once so that we reuse below
		$this->customPrefixLength = strlen( $this->get( 'customprefix' ) );

		// Extract rules on how to link through headings and values to wiki pages
		if ( $this->get( 'link' ) ) {
			foreach ( explode( ',', $this->get( 'link' ) ) as $linkedColumn ) {
				$parts = explode( '~', $linkedColumn );
				$format = 'link';
				if ( sizeof( $parts ) > 1 ) {
					$format .= '~' . $parts[1];
				}
				$this->formats[$parts[0]] = $format;
				$this->implictlyAddColumn( $parts[0] );
			}
		}

		// If lastcomment mode is selected then we require the keywords field and we
		// will set those to link through to the appropriate wiki page
		if ( $this->get( 'lastcomment' ) ) {
			$this->requireField( 'keywords' );
			if ( !array_key_exists( 'keywords', $this->formats ) ) {
				$this->formats['keywords'] = 'link~keyword';
			}
		}

		// Sorting does not work when grouping is enabled so we disable it
		if ( $this->get( 'group' ) ) {
			$this->set( 'sortable', '0' );
		}

		$db = $this->connector->connect();

		$this->initFieldIds( $db );
		$sql = $this->getSQL();

		if ( !$db ) {
			return $this->connector->getError();
		}

		$result = $this->connector->execute( $sql, $db );

		// Check that the record set is open
		if ( $result ) {
			if ( $this->get( 'format' ) == 'count' ) {
				while ( $line = $this->connector->fetch( $result ) ) {
					$output = $line['count'];
				}
			} else {
				$this->overrideFormats();
				$renderer = new BugzillaQueryRenderer( $this );

				if ( $this->connector->getRowCount( $result ) > 0 ) {
					$this->context->debug && $this->context->debug( 'Rendering results' );
					$output = $renderer->renderHTML( $result );
				} else {
					$this->context->debug && $this->context->debug( 'No results to render' );

					// If total is set then we still want to render the result
					// because we want the zero totals to show
					if ( $this->get( 'total' ) ) {
						$output = $renderer->renderHTML( $result );
					} else {
						$output = $renderer->renderNoResultsHTML();
					}
				}
			}

			$this->context->debug && $this->context->debug( 'Freeing up db result' );
			$this->connector->free( $result );
		} else {
			return $this->context->getErrorMessage( 'bugzillareports-sql-error',
				$sql . ' ' . $this->connector->getDbError( $db ) );
		}

		$this->connector->close( $db );

		if ( $this->context->debug ) {
			$output .= "<div>SQL = {$sql}</div>";
		}

		$this->context->debug &&
			$this->context->debug(
				'All done and returning page output : Number of characters in output = ' . strlen( $output )
			);
		$this->context->debug == 2 && $this->context->debug( 'Report HTML output is ' . $output );

		return $output;
	}

	/**
	 * Build the SQL for the query
	 *
	 * @return string SQL query string
	 */
	public function getSQL() {
		$this->context->debug && $this->context->debug( 'Rendering BugzillaQuery' );

		$where = '';

		// Process fields and make sure we have SQL and implicit usage built up
		foreach ( array_keys( $this->supportedParameters ) as $column ) {
			$fieldValue = $this->get( $column );
			if ( $fieldValue ) {
				$this->context->debug &&
					$this->context->debug( "Handling field : $column : $fieldValue" );
				$type = $this->supportedParameters[$column];

				// Support generic argument syntax
				if ( $type == 'filters' ) {
					$args = explode( '%26', $fieldValue );
					foreach ( $args as $arg ) {
						$this->context->debug( "Processing filter : $arg" );
						// Match for encoded =
						if ( preg_match( '/%3D/', $arg ) ) {
							$parts = explode( '%3D', $arg );
							$argColumn = $parts[0];
							$argFieldValue = $parts[1];
							if ( $this->isCustomField( $argColumn ) ) {
								$argType = 'field';
								$this->addCustomField( $argColumn );
							} else {
								$argType = $this->supportedParameters[$argColumn];
							}
							if ( substr( $argType, 0, 5 ) != 'field' ) {
								$this->context->warn( "$argColumn is not of type field so ignoring" );
							} else {
								$where .= $this->processField( $argColumn, $argFieldValue, $argType );
							}
						} else {
							$this->context->warn(
								"arg field not a valid format so ignoring , it should include '%3D', it was {$arg}"
							);
						}
					}
				} elseif ( $type == 'field-keywords' ) {
					$where .= 'AND EXISTS (SELECT keywordid,keywords.bug_id FROM ' .
							$this->connector->getTable( 'keywords' ) . ' AS keywords ' .
							' LEFT JOIN ' .
							$this->connector->getTable( 'keyworddefs' ) . ' AS keyworddefs ' .
							' ON keywords.keywordid=keyworddefs.id ' .
							' WHERE keywords.bug_id=bugs.bug_id ' .
							$this->processField( $column, $fieldValue, 'field' ) .
							')';
				} else {
					$where .= $this->processField( $column, $fieldValue, $type );
				}
			}
		}

		if ( $this->get( 'format' ) == 'list' ) {
			$this->requireField( 'to' );
			$this->requireField( 'deadline' );
		}

		if ( $this->get( 'flag' ) ) {
			$this->requireField( 'flag' );
			$this->implictlyAddColumn( 'flagfrom' );
			$this->implictlyAddColumn( 'flagname' );
			$this->implictlyAddColumn( 'flagdate' );
		}

		if ( $this->get( 'lastcomment' ) ) {
			$this->requireField( 'lastcomment' );
		}

		if ( $this->get( 'search' ) ) {
			$where .= " AND short_desc LIKE '%" . $this->get( 'search' ) . "%'";
		}

		// Set implicit group order
		if ( $this->get( 'group' ) && array_key_exists( $this->get( 'group' ), $this->fieldDefaultOrder ) ) {
			$this->setImplicit( 'grouporder', $this->fieldDefaultOrder[$this->get( 'group' )] );
		}

		// Quick flag enabled by default
		$this->requireField( 'quickflag' );

		// Alias enabled by default
		$this->requireField( 'alias' );

		// Prepare the query;
		$this->preSQLGenerate();

		$this->context->debug &&
			$this->context->debug( 'Columns required are ' . join( ',', array_keys( $this->fieldsRequired ) ) );

		$sql = '';

		if ( $this->get( 'format' ) == 'count' ) {
			$sql .= 'SELECT COUNT(DISTINCT(id)) AS count FROM (';
		}
		$sql .= 'SELECT DISTINCT bugs.bug_id AS id';
		if ( $this->isRequired( 'alias' ) ) {
			$sql .= ', aliases.alias AS alias';
		}
		if ( $this->isRequired( 'assigned' ) ) {
			$sql .= ', assignedactivity.bug_when AS assigned';
		}
		if ( $this->isRequired( 'attachments' ) ) {
			$sql .= ', attachments.nattachments AS attachments ';
		}
		if ( $this->isRequired( 'blocks' ) ) {
			$sql .= ', blockstab.blocks AS blocks, blockstab.blocksalias AS blocksalias, blockstab.blockssummary AS blockssummary,blockstab.blocksstatus AS blocksstatus, blockstab.blockspriority AS blockspriority, blockstab.realname AS blocksto';
		}
		if ( $this->isRequired( 'cc' ) ) {
			if ( $this->get( 'nameformat' ) == 'login' ) {
				$sql .= ', ccprofiles.login_name AS cc';
			} else {
				$sql .= ', ccprofiles.realname AS cc';
			}
		}
		if ( $this->isRequired( 'closed' ) ) {
			$sql .= ', closedactivity.bug_when AS closed';
		}
		if ( $this->isRequired( 'component' ) ) {
			$sql .= ', components.name AS component';
		}
		if ( $this->isRequired( 'created' ) ) {
			$sql .= ', creation_ts AS created';
		}
		if ( $this->isRequired( 'deadline' ) ) {
			$sql .= ', deadline';
		}
		if ( $this->isRequired( 'depends' ) ) {
			$sql .= ', dependstab.depends AS depends, dependstab.dependsalias AS dependsalias, dependstab.dependssummary AS dependssummary,dependstab.dependsstatus AS dependsstatus, dependstab.dependspriority AS dependspriority, dependstab.realname AS dependsto';
		}
		if ( $this->isRequired( 'flag' ) ) {
			if ( $this->get( 'nameformat' ) == 'login' ) {
				$sql .= ', flagprofiles.flagfrom_login AS flagfrom';
				$sql .= ', flagprofiles.flag_login AS flag';
			} else {
				$sql .= ', flagprofiles.flagfrom_realname AS flagfrom';
				$sql .= ', flagprofiles.flag_realname AS flag';
			}
			$sql .= ', flagprofiles.flagname AS flagname';
			$sql .= ', flagprofiles.flagdate AS flagdate';
		} elseif ( $this->isRequired( 'quickflag' ) ) {
			$sql .= ', quickflag.flagdate AS flagdate';
		}
		if ( $this->isRequired( 'estimated' ) ) {
			$sql .= ', estimated_time AS estimated';
		}
		if ( $this->isRequired( 'from' ) ) {
			if ( $this->get( 'nameformat' ) == 'login' ) {
				$sql .= ', reporterprofiles.login_name AS raisedby';
			} else {
				$sql .= ', reporterprofiles.realname AS raisedby';
			}
		}
		if ( $this->isRequired( 'hardware' ) ) {
			$sql .= ', rep_platform AS hardware';
		}
		if ( $this->isRequired( 'keywords' ) ) {
			$sql .= ', (SELECT GROUP_CONCAT(keyworddefs.name) FROM bugs.keyworddefs WHERE keyworddefs.id IN (SELECT keywords.keywordid FROM bugs.keywords WHERE keywords.bug_id=bugs.bug_id )) AS keywords';
		}
		if ( $this->isRequired( 'milestone' ) ) {
			$sql .= ', target_milestone AS milestone';
		}
		if ( $this->isRequired( 'lastcomment' ) ) {
			$sql .= ', longdescslastcomment.thetext';
		}
		if ( $this->isRequired( 'modified' ) ) {
			$sql .= ', lastdiffed AS modified';
		}
		if ( $this->isRequired( 'os' ) ) {
			$sql .= ', op_sys AS os';
		}
		// Priority always required because it used as class name for bug row
		$sql .= ', priority';
		if ( $this->isRequired( 'product' ) ) {
			$sql .= ', products.name AS product';
		}
		if ( $this->isRequired( 'qa' ) ) {
			if ( $this->get( 'nameformat' ) == 'login' ) {
				$sql .= ', qaprofiles.login_name AS qa';
			} else {
				$sql .= ', qaprofiles.realname AS qa';
			}
		}
		if ( $this->isRequired( 'remaining' ) ) {
			$sql .= ', remaining_time AS remaining';
		}
		if ( $this->isRequired( 'reopened' ) ) {
			$sql .= ', reopenedactivity.bug_when AS reopened';
		}
		if ( $this->isRequired( 'resolution' ) ) {
			$sql .= ', resolution';
		}
		if ( $this->isRequired( 'resolved' ) ) {
			$sql .= ', resolvedactivity.bug_when AS resolved';
		}
		// Severity always required because it used as class name for bug row
		$sql .= ', bug_severity AS severity';
		if ( $this->isRequired( 'status' ) ) {
			$sql .= ', bug_status AS status';
		}
		if ( $this->isRequired( 'summary' ) ) {
			$sql .= ', short_desc AS summary';
		}
		if ( $this->isRequired( 'to' ) ) {
			if ( $this->get( 'nameformat' ) == 'login' ) {
				$sql .= ', profiles.login_name AS assignedto';
			} else {
				$sql .= ', profiles.realname AS assignedto';
			}
		}
		if ( $this->isRequired( 'url' ) ) {
			$sql .= ', bug_file_loc AS url';
		}
		if ( $this->isRequired( 'version' ) ) {
			$sql .= ', version';
		}
		if ( $this->isRequired( 'votes' ) ) {
			$sql .= ', votes';
		}
		if ( $this->isRequired( 'work' ) ) {
			$sql .= ', SUM(longdescswork.work_time) AS work';
		}

		// Now add custom fields
		$this->context->debug &&
			$this->context->debug( sizeof( $this->requiredCustomFields ) . ' custom fields' );

		if ( sizeof( $this->requiredCustomFields ) > 0 ) {
			foreach ( $this->requiredCustomFields as $column ) {
				$sql .= ", $column";
			}
		}
		$sql .= ' FROM ' . $this->connector->getTable( 'bugs' );
		if ( $this->isRequired( 'assigned' ) ) {
			$sql .= ' LEFT JOIN ' .
				' (SELECT bug_id, MAX(bug_when) AS bug_when FROM ' .
				$this->connector->getTable( 'bugs_activity' ) .
				' WHERE fieldid=' . BugzillaQuery::$fieldIds['bug_status'] .
				" AND added='ASSIGNED' GROUP BY bug_id) AS assignedactivity ON bugs.bug_id=assignedactivity.bug_id";
		}
		if ( $this->isRequired( 'attachments' ) ) {
			$sql .= ' LEFT JOIN (SELECT bug_id as attachmentbugid, COUNT(attach_id) AS nattachments FROM '.
				$this->connector->getTable( 'attachments' ) .
				' GROUP BY attachmentbugid) AS ' .
				'attachments ON attachments.attachmentbugid=bugs.bug_id';
		}
		if ( $this->isRequired( 'blocks' ) ) {
			$sql .= ' LEFT JOIN (SELECT dependson,blocked AS blocks, blockedalias.alias AS blocksalias, blockedbugs.short_desc AS blockssummary, blockedbugs.bug_status AS blocksstatus, blockedbugs.priority AS blockspriority,login_name,realname FROM ' .
				$this->connector->getTable( 'dependencies' )
				. ' INNER JOIN ' .
				$this->connector->getTable( 'bugs' ) .
				' AS blockedbugs ON dependencies.blocked=blockedbugs.bug_id' .
				' LEFT JOIN ' .
				$this->connector->getTable( 'bugs_aliases' ) .
				' AS blockedalias ON blockedalias.bug_id=blockedbugs.bug_id' .
				' INNER JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' ON blockedbugs.assigned_to=profiles.userid' .
				' WHERE 1=1 ' . $this->getWhereClause( $this->get( 'dependsstatus' ), 'blockedbugs.bug_status' ) .
				' ORDER BY blockedbugs.priority' .
				') AS blockstab ON blockstab.dependson=bugs.bug_id';
		}
		if ( $this->isRequired( 'component' ) ) {
			$sql .= ' LEFT JOIN ' .
				$this->connector->getTable( 'components' ) .
				' on bugs.component_id=components.id';
		}
		if ( $this->isRequired( 'cc' ) ) {
			$sql .= ' INNER JOIN (SELECT bug_id,login_name,realname FROM ' .
				$this->connector->getTable( 'cc' ) .
				' INNER JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' ON cc.who=profiles.userid';
			if ( $this->get( 'cc' ) ) {
				$sql .= $this->getWhereClause( $this->get( 'cc' ), 'profiles.login_name' );
			}
			$sql .= ') AS ' .
				'ccprofiles ON ccprofiles.bug_id=bugs.bug_id';
		}
		if ( $this->isRequired( 'closed' ) ) {
			$sql .= ' LEFT JOIN ' .
				' (SELECT bug_id, MAX(bug_when) AS bug_when FROM ' .
				$this->connector->getTable( 'bugs_activity' ) .
				' WHERE fieldid=' . BugzillaQuery::$fieldIds['bug_status'] .
				" AND added='CLOSED' GROUP BY bug_id) AS closedactivity ON bugs.bug_id=closedactivity.bug_id";
		}
		if ( $this->isRequired( 'depends' ) ) {
			$sql .= ' LEFT JOIN (SELECT blocked,dependson AS depends, dependsonalias.alias AS dependsalias, dependsonbugs.short_desc AS dependssummary, dependsonbugs.bug_status AS dependsstatus, dependsonbugs.priority AS dependspriority, login_name, realname FROM ' .
				$this->connector->getTable( 'dependencies' )
				. ' INNER JOIN ' .
				$this->connector->getTable( 'bugs' ) .
				' AS dependsonbugs ON dependencies.dependson=dependsonbugs.bug_id' .
				' LEFT JOIN ' . $this->connector->getTable( 'bugs_aliases' ) .
				' AS dependsonalias ON dependsonalias.bug_id=dependsonbugs.bug_id' .
				' INNER JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' ON dependsonbugs.assigned_to=profiles.userid' .
				' WHERE 1=1 ' . $this->getWhereClause( $this->get( 'dependsstatus' ), 'dependsonbugs.bug_status' ) .
				' ORDER BY dependsonbugs.priority' .
				') AS dependstab ON dependstab.blocked=bugs.bug_id';
		}
		if ( $this->isRequired( 'flag' ) ) {
			$sql .= ' INNER JOIN (SELECT bug_id,creation_date AS flagdate,flagsto.login_name AS flag_login,flagsto.realname AS flag_realname,flagsfrom.login_name AS flagfrom_login, flagsfrom.realname AS flagfrom_realname,flagtypes.name AS flagname FROM ' .
				$this->connector->getTable( 'flags' ) .
				' INNER JOIN ' .
				$this->connector->getTable( 'flagtypes' ) .
				' ON flags.type_id=flagtypes.id INNER JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' AS flagsto ON flags.requestee_id=flagsto.userid INNER JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				" AS flagsfrom ON flags.setter_id=flagsfrom.userid WHERE status='?'";
			if ( $this->get( 'flag' ) ) {
				$sql .= $this->getWhereClause( $this->get( 'flag' ), 'flagsto.login_name' );
			}
			$sql .= ') as ' .
				'flagprofiles on flagprofiles.bug_id=bugs.bug_id';
		} elseif ( $this->isRequired( 'quickflag' ) ) {
			$sql .= ' LEFT JOIN (SELECT bug_id AS quickflagbugid, MAX(creation_date) AS flagdate FROM ' .
				$this->connector->getTable( 'flags' ) .
				" WHERE status='?' GROUP BY quickflagbugid) AS " .
				'quickflag on quickflag.quickflagbugid=bugs.bug_id';
		}
		if ( $this->isRequired( 'from' ) ) {
			$sql .= ' LEFT JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' AS reporterprofiles ON bugs.reporter=reporterprofiles.userid';
		}
		if ( $this->isRequired( 'lastcomment' ) ) {
			$sql .= ' LEFT JOIN (SELECT MAX(longdescs.bug_when) AS sub_comment_when, ' .
				'longdescs.bug_id AS sub_bug_id FROM ' .
				$this->connector->getTable( 'longdescs' ) .
				' GROUP BY longdescs.bug_id) ' .
				'descs ON bugs.bug_id=descs.sub_bug_id LEFT JOIN ' .
				$this->connector->getTable( 'longdescs' ) . ' AS longdescslastcomment ON ' .
				'longdescslastcomment.bug_when=sub_comment_when';
		}
		if ( $this->isRequired( 'product' ) ) {
			$sql .= ' LEFT JOIN ' .
				$this->connector->getTable( 'products' ) .
				' ON bugs.product_id=products.id';
		}
		if ( $this->isRequired( 'qa' ) ) {
			$sql .= ' LEFT JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' AS qaprofiles ON bugs.qa_contact=qaprofiles.userid';
		}
		if ( $this->isRequired( 'reopened' ) ) {
			$sql .= ' LEFT JOIN ' .
				' (SELECT bug_id, MAX(bug_when) AS bug_when FROM ' .
				$this->connector->getTable( 'bugs_activity' ) .
				' WHERE fieldid=' . BugzillaQuery::$fieldIds['bug_status'] .
				" AND added='REOPENED' GROUP BY bug_id) AS reopenedactivity ON bugs.bug_id=reopenedactivity.bug_id";
		}
		if ( $this->isRequired( 'resolved' ) ) {
			$sql .= ' LEFT JOIN ' .
				' (SELECT bug_id, MAX(bug_when) AS bug_when FROM ' .
				$this->connector->getTable( 'bugs_activity' ) .
				' WHERE fieldid=' . BugzillaQuery::$fieldIds['bug_status'] .
				" AND added='RESOLVED' GROUP BY bug_id) AS resolvedactivity ON bugs.bug_id=resolvedactivity.bug_id";
		}
		if ( $this->isRequired( 'to' ) ) {
			$sql .= ' LEFT JOIN ' .
				$this->connector->getTable( 'profiles' ) .
				' ON bugs.assigned_to=profiles.userid';
		}
		if ( $this->isRequired( 'verified' ) ) {
			$sql .= ' LEFT JOIN ' .
				' (SELECT bug_id, MAX(bug_when) AS bug_when from ' .
				$this->connector->getTable( 'bugs_activity' ) .
				' WHERE fieldid=' . BugzillaQuery::$fieldIds['bug_status'] .
				" AND added='VERIFIED' GROUP BY bug_id) AS verifiedactivity ON bugs.bug_id=verifiedactivity.bug_id";
		}
		if ( $this->isRequired( 'alias' ) ) {
			$sql .= ' LEFT JOIN ' .
				$this->connector->getTable( 'bugs_aliases' ) .
				' AS aliases ON aliases.bug_id=bugs.bug_id';
		}
		if ( $this->isRequired( 'work' ) ) {
			$sql .= ', ' . $this->connector->getTable( 'longdescs' ) . ' AS longdescswork';
		}
		$sql .= ' WHERE 1=1 ' . $where;
		if ( $this->isRequired( 'work' ) ) {
			$sql .= ' AND longdescswork.bug_id=bugs.bug_id GROUP BY bugs.bug_id';
		}
		$sql .= ' ORDER BY ' .
			$this->getMappedSort() . ' ' . $this->getOrder();
		if ( $this->get( 'format' ) == 'count' ) {
			$sql .= ') AS b';
		}
		$sql .= ';';

		$this->context->debug && $this->context->debug( 'SQL : ' . $sql );

		return $sql;
	}

	/**
	 * Process a field
	 *
	 * @param string $column
	 * @param string $fieldValue
	 * @param string $type
	 * @return string WHERE clause fragment (or emptiness)
	 */
	public function processField( $column, $fieldValue, $type ) {
		$where = '';
		switch ( $type ) {
			case 'field-id':
				if ( !strpos( $fieldValue, ',' ) ) {
					$this->explitlyOneValue = true;
					$this->context->debug &&
						$this->context->debug( 'Explicitly one value' );
				}
			case 'field':
			case 'field-filter':
			case 'field-date':
			case 'field-number':
				if ( $type != 'field-id' ) {
					// If field is multiple values ",", not a value
					// "!", any value "*", a non-null (or
					// positive value) "+", then add column and
					// sort, otherwise remove it.
					if ( preg_match( "/[,!+\*%<>]/", $fieldValue ) ) {
						$this->implictlyAddColumn( $column );
						if ( array_key_exists( $column, $this->fieldDefaultOrder ) ) {
							$this->setImplicit( 'sort', "$column" );
							$this->setImplicit( 'order', $this->fieldDefaultOrder[$column] );
						}
					} else {
						$this->implictlyRemoveColumn( $column );
					}
				}
			case 'field-depends':
				$sqlColumn = $column;
				if ( array_key_exists( $column, $this->fieldSQLColumn ) ) {
					$sqlColumn = $this->fieldSQLColumn[$column];
				}

				switch ( $type ) {
					case 'field-id':
					case 'field':
					case 'field-depends':
						$where = $this->getWhereClause( $fieldValue, $sqlColumn );
						break;
					case 'field-number':
						$where = $this->getIntWhereClause( $fieldValue, $sqlColumn );
						break;
					case 'field-date':
						$where = $this->getDateWhereClause( $fieldValue, $sqlColumn );
				}

				$this->requireField( $column );

				// Create the bugzilla query URL
				$bzFieldName = $column;
				if ( array_key_exists( $column, $this->fieldBZQuery ) ) {
					$bzFieldName = $this->fieldBZQuery[$column];
				}

				$this->bzURL .= $this->getBZQuery( $fieldValue, $bzFieldName );

				break;
		}

		return $where;
	}

	public function getMatchExpression( $match, $name, $negate ) {
		$trimmedMatch = trim( $match );
		$controlCharacter = substr( $trimmedMatch, 0, 1 );
		$localNegate = $negate;
		$range = 0;

		$this->context->debug &&
			$this->context->debug( 'Control character is ' . $controlCharacter );

		if ( preg_match( "/[!><]/", $controlCharacter ) ) {
			$trimmedMatch = substr( $trimmedMatch, 1 );
			switch ( $controlCharacter ) {
				case '!':
					$localNegate = !$negate;
					break;
				case '>':
				case '<':
					$range = 1;
					break;
			}
		}

		$decodedMatch = $this->safeSQLdecode( $trimmedMatch );

		if ( preg_match( '/%/', $match ) ) {
			// We have a like clause
			if ( $localNegate ) {
				return $name . " not like '" . $decodedMatch . "'";
			} else {
				return $name . " like '" . $decodedMatch . "'";
			}
		} elseif ( $range ) {
			if ( $controlCharacter == '<' ) {
				if ( $localNegate ) {
					return $name . " > '" . $decodedMatch . "'";
				} else {
					return $name . " <'" . $decodedMatch . "'";
				}
			} else {
				if ( $localNegate ) {
					return $name . " < '" . $decodedMatch . "'";
				} else {
					return $name . " > '" . $decodedMatch . "'";
				}
			}
		} else {
			if ( $localNegate ) {
				return $name . "<>'" . $decodedMatch . "'";
			} else {
				return $name . "='" . $decodedMatch . "'";
			}
		}
	}

	/**
	 * Get the BZ Query URL
	 *
	 * @param string $value
	 * @param string $name
	 * @return string
	 */
	private function getBZQuery( $value, $name ) {
		if ( preg_match( "/^[\*+-]/", $value ) ) {
			// *,+ and - (i.e. any value, not null/not zero and null/zero)
			// not supported in Bugzilla queries
			return '';
		}

		// Replace spaces with "%20" to make it a safe URL
		$safeValue = str_replace( ' ', '%20', $value );
		$query = '';
		$bzFieldGroupCount = 0;
		$pos = strpos( $safeValue, '!(' );
		$operator;
		$negate;

		if ( $pos === false ) {
			$pos = strpos( $safeValue, '!' );
			if ( $pos === false ) {
				$operator = 'OR';
				$negate = false;
			} else {
				$operator = 'AND';
				$negate = true;
				$safeValue = substr( $safeValue, 1 );
			}
		} else {
			$safeValue = substr( $safeValue, 2, -1 );
			$operator = 'AND';
			$negate = true;
		}

		foreach ( explode( ',', $safeValue ) as $singleValue ) {
			$fieldName = '0-' . $this->bzFieldCount . '-' . $bzFieldGroupCount;
			if ( $negate ) {
				$query .= "&field$fieldName=$name" .
					"&type$fieldName=notequals" .
					"&value$fieldName=$singleValue";
			} else {
				$query .= "&field$fieldName=$name" .
					"&type$fieldName=equals" .
					"&value$fieldName=$singleValue";
			}

			// Not operator leads to anding ...
			if ( $operator == 'AND' ) {
				$this->bzFieldCount++;
			} else {
				// ... otherwise it's oring ...
				$bzFieldGroupCount++;
			}
		}

		if ( $operator == 'OR' ) {
			$this->bzFieldCount++;
		}

		return $query;
	}

	/**
	 * Override formats
	 */
	private function overrideFormats() {
		if ( $this->get( 'modifiedformat' ) ) {
			$this->context->debug &&
				$this->context->debug( 'Setting modified format to ' . $this->get( 'modifiedformat' ) );
			$this->formats['modified'] = $this->get( 'modifiedformat' );
		}
		if ( $this->get( 'createdformat' ) ) {
			$this->context->debug &&
				$this->context->debug( 'Setting created format to ' . $this->get( 'createdformat' ) );
			$this->formats['created'] = $this->get( 'createdformat' );
		}
	}

	/**
	 * A field is identified as a custom one if
	 *
	 *	 1) implicitcustom field is false and
	 *		 a) it starts with the custom field prefix, or
	 *		 b) it is listed in the supportedCustomFields
	 * or 2) implictcustom field is true and
	 *	 a) it is not in the supportParameters list
	 *
	 * Note that nowadays Bugzilla enforces custom fields to start with "cf_"
	 *
	 * @param string $column
	 * @return bool
	 */
	protected function isCustomField( $column ) {
		if ( $this->get( 'implicitcustom' ) == 'true' ) {
			$flag = !array_key_exists( $column, $this->columnName );
			$flag && $this->context->debug && $this->context->debug( "$column is an implicit custom field" );
			return $flag;
		} else {
			$flag = substr( $column, 0, $this->customPrefixLength ) == $this->get( 'customprefix' ) ||
				array_key_exists( $column, $this->supportedCustomFields );
			$flag && $this->context->debug && $this->context->debug( "$column is an explicit custom field" );
			return $flag;
		}
	}

	/**
	 * Register the supplied field as a custom one
	 *
	 * @param string $column
	 */
	protected function addCustomField( $column ) {
		array_push( $this->requiredCustomFields, $column );
		$this->context->debug && $this->context->debug( "Custom field added $column" );
	}

	/**
	 * @param PgSql\Connection|mysqli|false $db
	 * @return array
	 */
	protected function initFieldIds( $db ) {
		// Initialise the fieldIds
		if ( sizeof( BugzillaQuery::$fieldIds ) <= 1 ) {
			$result = $this->connector->execute( 'select id,name from ' . $this->connector->getTable( 'fielddefs' ), $db );
			$this->context->debug &&
				$this->context->debug( 'Registering field ids : ' . $this->connector->getRowCount( $result ) );
			while ( $line = $this->connector->fetch( $result ) ) {
				BugzillaQuery::$fieldIds[$line['name']] = $line['id'];
				$this->context->debug &&
					$this->context->debug( 'Registering field id' . $line['name'] . ' -> ' . $line['id'] );
			}
		} else {
			$this->context->debug &&
				$this->context->debug( 'Field ids already initialised : ' . sizeof( BugzillaQuery::$fieldIds ) );
		}
		return BugzillaQuery::$fieldIds;
	}

}
