<?php

$filename = $argv[1];
$folder = $argv[2];

$commandLineInfo = "\nCOMMAND: php ".basename(__FILE__)." <input-dumpfile> <output-directory>\n";

if ($argc < 3)
	die("\nERROR: Incorrect number of parameters.\n$commandLineInfo\n");

if (!file_exists($filename))
	die("\nERROR: Input file does not exist.\n$commandLineInfo\n");

// TODO: check if the directory is writable
if (!is_dir($folder))
	die("\nERROR: Output directory does not exist.\n$commandLineInfo\n");


$handle = @fopen($filename, "r");

$untouchedEntries = 0;
$filteredEntries = 0;

while (($buffer = fgets($handle)) !== false) {
	
//	echo $buffer;
	
	$key = explode("\t", $buffer)[0];
	$errorString = "\\\"";
	
	if (strpos($key, $errorString) > 0)
		{
		$filteredEntries += 1;
		$filteredFile = "$folder/RTCE_UNPARSABLE_DUMP_ENTRIES.txt";
		$fh = fopen($filteredFile, 'a') or die("can't open file");
		fwrite($fh, $buffer);
		fclose($fh);
		}
	else 
		{
			$untouchedEntries += 1;
			$filteredFile = "$folder/RTCE_PARSABLE_DUMP.txt";
			$fh = fopen($filteredFile, 'a') or die("can't open file");
			fwrite($fh, $buffer);
			fclose($fh);
		}
	
}

if (!feof($handle)) {
	die("Error: unexpected fgets() fail\n");
}

$totalEntries = $filteredEntries + $untouchedEntries;

echo "\nTotal entries: ".$totalEntries;
echo "\nRemoved entries (keys with escaping characters): ".$filteredEntries;
echo "\nUntouched entries: ".$untouchedEntries;
echo "\n\n";

fclose($handle);

?>