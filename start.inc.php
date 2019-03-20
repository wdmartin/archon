<?php
/**
 * Initalizes the Archon system
 *
 * @package Archon
 * @author Chris Rishel
 */



if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	set_magic_quotes_runtime(false);
}

if(!$_REQUEST)
{
   $_REQUEST = array();
}

if(get_magic_quotes_gpc())
{
   $_REQUEST = map_recursive('stripslashes', $_REQUEST);
}

$arrP = explode('/', $_REQUEST['p']);

if(file_exists('packages/core/install/install.php') && $arrP[0] != 'admin' && $_REQUEST['p'] != 'install' && $_REQUEST['p'] != 'upgrade')
{
   trigger_error("packages/core/install/install.php MUST be deleted before Archon will function!", E_USER_ERROR);
}

$_ARCHON->DefaultOBLevel = ob_get_level();

if($_FILES)
{
   $_FILES = array_change_key_case_recursive($_FILES);
}

$_REQUEST = array_change_key_case_recursive($_REQUEST);

$_SERVER['REQUEST_URI'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "{$_SERVER['SCRIPT_NAME']}?{$_SERVER['QUERY_STRING']}";

if(!$_ARCHON->db->ServerType)
{
   trigger_error("Fatal Error: The database server type is not configured in config.inc.php.", E_USER_ERROR);
}
elseif(!$_ARCHON->db->ServerAddress)
{
   trigger_error("Fatal Error: The database server address is not configured in config.inc.php.", E_USER_ERROR);
}
elseif(!$_ARCHON->db->Login)
{
   trigger_error("Fatal Error: The database server login is not configured in config.inc.php.", E_USER_ERROR);
}

// Connect to the database
if($_ARCHON->db->ServerType == 'MSSQL')
{
   // these are necessary to prevent freetds from truncating large fields
   putenv("TDSVER=70");

   ini_set("mssql.textsize", 2147483647);
   ini_set("mssql.textlimit", 2147483647);
}

// Build the database data source name (DSN).
$dbdsn = strtolower($_ARCHON->db->ServerType).":";
$dbdsn .= "host=".$_ARCHON->db->ServerAddress.";";
$dbdsn .= "port=".$_ARCHON->db->ServerPort.";";
$dbdsn .= "dbname=".$_ARCHON->db->DatabaseName.";";
$dbdsn .= "charset=utf8";

// Build an array of default connection options for the database connection.
$dboptions = array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Failed database calls throw PHP exceptions
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Results returned in associative arrays
);

// Allow MySQL to handle very large requests.
if($_ARCHON->db->ServerType == 'MYSQL'){
	$dboptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET max_allowed_packet=1073741824";
}

//I don't think PDO offers a way to redirect DB errors to a log
//like this.  Maybe something with a PHP-wide set handler?
//$_ARCHON->QueryLog = New QueryLog();

try {
	$db = new PDO (
		$dbdsn,
		$_ARCHON->db->Login,
		$_ARCHON->db->Password,
		$dboptions
	);

	// Older versions of PHP do not properly set the character set based
	// on the DSN when using MySQL.  If running on one of these very old
	// versions, we need to set the encoding for the database connection
	// manually.  Any PHP from 5.36 forward does not need this.
	if (version_compare(PHP_VERSION, '5.3.6', '<') && $_ARCHON->db->ServerType == 'MYSQL') {
    	$db->query("SET NAMES UTF8");
	}


	$_ARCHON->pdo = $db;
} catch(PDOException $e) {

	$SQLerror = "Could not connect with database.\n";

	if(!defined('PDO::ATTR_DRIVER_NAME'))
	{
		$SQLerror .= "Your PHP does not have PDO installed.\n";
	}

	if(!extension_loaded('pdo_mysql') && $_ARCHON->db->ServerType === 'MYSQL')
	{
		$SQLerror .= "Your PHP does not have the MySQL driver for PDO installed.\n";
	}

	if(!extension_loaded('pdo_dblib') && $_ARCHON->db->ServerType === 'MSSQL')
	{
		$SQLerror .= "Your PHP does not have the MSSQL driver for PDO installed.\n";
	}

	$debug = false;

	if(error_reporting() === E_ALL){ $debug = true; }
	if(error_reporting() === (E_ALL ^ E_NOTICE)){ $debug = true; }

	if($debug){
		$SQLerror .= "The full error is:\n $error";
	} else {
		$SQLerror .= 'For further details enable debug mode in includes.inc.php.';
	}

	header('Content-Type: text/plain');
	print $SQLerror;
	exit;
}

if($_ARCHON->pdo)
{
   if($_REQUEST['p'] != 'install' && $_REQUEST['p'] != 'upgrade')
   {
      $_ARCHON->initialize();
   }
   elseif($_REQUEST['p'] == 'install')
   {
      $_ARCHON->Script = 'packages/core/install/install.php';
   }
   else
   {
      $_ARCHON->Script = 'packages/core/install/upgrade.php';
   }

   if($_ARCHON->Disabled || (!CONFIG_CORE_PUBLIC_ENABLED && $arrP[0] != 'admin' && $_REQUEST['p'] != 'install' && $_REQUEST['p'] != 'upgrade'))
   {
      if(!$_ARCHON->Security->userHasAdministrativeAccess())
      {
         include('header.inc.php');
         echo('<div class="center bold">' . CONFIG_CORE_DISABLED_MESSAGE . '</div>');
         include('footer.inc.php');
         exit();
      }
      else
      {
         $_ARCHON->PublicInterface->Header->Message = $_ARCHON->PublicInterface->Header->Message ? $_ARCHON->PublicInterface->Header->Message."\n Archon is currently closed to the public.\n" : "Archon is currently closed to the public.\n";
      }
   }
}

?>
