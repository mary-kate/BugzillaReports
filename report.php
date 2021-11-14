<?php
#
# This script is a demonstration of how you can use the Bugzilla Reports 
# module outside of MediaWiki.  Although the standard usage is within 
# MediaWiki and this does give great control of integrating your reports 
# with your content, you may wish to include these reports within any other
# kind of PHP application.  Well here you go ... a quick example for you and
# then the World's your oyster ...
#

/**
 * Copyright (C) 2008 - Ian Homer & bemoko
 */

require_once("init.php");

#
# Uncomment the following and set your configuration parameters to enable 
# standalone reports outside of MediaWiki and in your PHP application of your
# choice
#
#define('BUGZILLAREPORTS',1);
#$bzScriptPath="";
#$wgBugzillaReports = [
#	'host'		=> "localhost",
#	'database'	=> "bugs",
#	'user'		=> "bugs",
#	'password'	=> "password",
#	'bzserver'  => "http://myserver",
#	'maxrows'	=> "300"
#];
if ( !defined('BUGZILLAREPORTS')  ) {
	die('This Bugzilla Reports script has not been enabled' );
}

$parser=new BParser();
$bugzillaReport = new BugzillaReport( $parser );
$bugzillaReport->setRawHTML(true);

#
# See http://www.mediawiki.org/wiki/Extension:Bugzilla_Reports#Usage for
# documentation on parameters available
#

$out=$bugzillaReport->render(["priority=P1,P2","lastcomment=1"]);
?>
<html>
  <head>
	<?=$parser->mOutput->head?>
  </head>
  <body>
	<?=$out?>
  </body>
</html>
