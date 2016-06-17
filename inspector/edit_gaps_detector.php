<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

ini_set('memory_limit', '4096M');

include_once 'lib/history/history-utils.php';

$folder =  $argv[1];
$md5option =  $argv[2];

$commandLineInfo = "\nCOMMAND: php ".basename(__FILE__)." <pads_directory> \n";

if (($argc != 2) && ($argc != 3))
	die("\nERROR: Incorrect number of parameters.\n$commandLineInfo\n");

if (!is_dir($folder))
	die("\nERROR: Input directory does not exist.\n$commandLineInfo\n");

$files = scandir($folder);

foreach($files as $file) {
	
	$edits_file_path = $folder."/".$file;
	
	$json_history_string = file_get_contents($edits_file_path);
	
	if (checkIfContainsEditGaps($json_history_string))
		{
			$msg = $file;
			if ($md5option == "-md5")
				$msg = md5($file);
			
			echo $msg . "\n";
		}
		
}

?>