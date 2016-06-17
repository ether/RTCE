<?php

function printHistoryDebug($jsonChanges, $typeOfCollaboration)
{
	echo "\n";
	for ($i = 0; $i < count($jsonChanges); $i++) {

		$auth = ($jsonChanges[$i]["Author"] == "") ? "* ETHERPAD *" : $jsonChanges[$i]["Author"];

		$shortTimestamp = substr($jsonChanges[$i]["Timestamp"], 0, -3);
		
		$formattedTimestamp = date('F, jS  h:i:s A', $shortTimestamp);

		$spaceLabel = ($jsonChanges[$i]["spaceCollaboration"] == TRUE) ? " S " : "";
		$timeLabel = ($jsonChanges[$i]["timeCollaboration"] == TRUE) ? " T " : "";
		$spacetimeLabel = ($jsonChanges[$i]["spacetimeCollaboration"] == TRUE) ? " ST " : "";
		
		
		echo "\n[$i] ".
			 $jsonChanges[$i]["Revision"]." ".
			 $jsonChanges[$i]["opCode"].
			 "([".implode($jsonChanges[$i]["preInterval"],',')."][".implode($jsonChanges[$i]["postInterval"],',')."]) ".
			 "  ( by $auth )  ".
			 "<$formattedTimestamp [".$jsonChanges[$i]["Timestamp"]."]>".
			 " ---> ".
			$spaceLabel.
			$timeLabel.
			$spacetimeLabel;


		printMsgCollaboration($jsonChanges[$i][$typeOfCollaboration]);

		echo "\n";
}

echo "\n";
}

function printMsgCollaboration($boolean)
{
	echo ($boolean) ? "Collaborative!!!" : "";
}


?>