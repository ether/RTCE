<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

ini_set('memory_limit', '4096M');

//$execution_time_start = microtime(true);

include_once 'lib/history/history-utils.php';
include_once 'lib/history/history-print-utils.php';
include_once 'lib/history/history-stats-utils.php';

date_default_timezone_set('Europe/Paris');

$jsonFilePath =  $argv[1];
$spaceWindow = $argv[2];
$timeWindow = $argv[3];

$serializeHistoriesOption = $argv[4];
$tmpDir = $argv[5];

$commandLineInfo = "\nCOMMAND: php ".basename(__FILE__)." <padfile> <space-window-chars> <time-window-seconds> [-s <tmp_directory>] \n";

if (($argc != 4) && ($argc != 6))
	die("\nERROR: Incorrect number of parameters.\n$commandLineInfo\n");

if (((string)(int)$spaceWindow != $spaceWindow))
	die("\nERROR: The space window must be an integer.\n$commandLineInfo\n");

if ($spaceWindow < 0)
	die("\nERROR: The space window cannot be a negative value.\n$commandLineInfo\n");

if ((string)(int) $timeWindow != $timeWindow)
	die("\nERROR: The space window must be an integer.\n$commandLineInfo\n");

if ($timeWindow < 0)
	die("\nERROR: The time window cannot be a negative value.\n$commandLineInfo\n");


$historyWorkingDir = NULL;
if ($serializeHistoriesOption == "-s")
	{
	if ( ! ( is_dir( $tmpDir )))
		die("\nERROR - You asked to serialise histories but there's something wrong with the TMP directory...\n\n");
	
	$historyWorkingDir = $tmpDir . pathinfo($tmpDir . $jsonFilePath)["filename"];
	
	if (!(mkdir($historyWorkingDir)))
		die("\nERROR in creating TMP directory ". $historyWorkingDir."\n\nPlease empty $tmpDir and try again.\n\n");
	
	}
	
$timeWindow = $timeWindow * 1000;


/*
 * Read input file (lines, one for each change in JSON) and convert in one single JSON
*/
$jsonString = file_get_contents($jsonFilePath);
	
/*
 * Convert JSON (lines) to array
*/
$longJSON = lines2JSON($jsonString);

$jsonChanges = json_decode($longJSON, true);

/*
 * Sort changes by revision number
*/
usort($jsonChanges, "compareRevisionNumbers");
	
/***
addTimeCollaborationToJSONArray($jsonChanges, $timeWindow);

// Both space and spacetime collaboration are calculated in the same round
addSpaceCollaborationToJSONArray($jsonChanges, $spaceWindow, $timeWindow, $historyWorkingDir);
***/

addAllCollaborationsToJSONArray($jsonChanges, $spaceWindow, $timeWindow, $historyWorkingDir);

//printHistoryDebug($jsonChanges,"space");




$stats = getEditStats($jsonChanges);
	
echo md5(pathinfo($jsonFilePath)["filename"]) . ", " . $stats["avg-doc-length"] . ", " . $stats["total"] . ", " . $stats["authors_total"] . ", " .$stats["space"] . ", " .$stats["authors_space"] . ", " .$stats["time"]  . ", "  .$stats["authors_time"]  . ", " .$stats["spacetime"] .  ", " .$stats["authors_spacetime"] ."\n";


//$execution_time = (microtime(true) - $execution_time_start) / 60;
//echo "\nScript executed in $execution_time mins.\n\n";

?>