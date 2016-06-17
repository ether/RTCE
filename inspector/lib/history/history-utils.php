<?php

/***
 * Build an array of $times references to the same operation $opRef
 */
function generateMultipleOpRefs($opRef, $times) {
	
	$opRefs = [];
	
	for ($i = 0; $i < $times; $i++) {
		$opRefs[] = $opRef;
	}
	
	return $opRefs;
}


/***
 * Compare two changes by comparing their revision number
*/
function compareRevisionNumbers($a, $b)
{
	
	$aInteger = (int) explode(".",$a["Revision"])[0];
	$aDecimal = (int) explode(".",$a["Revision"])[1];

	$bInteger = (int) explode(".",$b["Revision"])[0];
	$bDecimal = (int) explode(".",$b["Revision"])[1];
	
	$comp = 1;
	if ($aInteger < $bInteger) $comp = -1;
	elseif (($aInteger == $bInteger) && ($aDecimal < $bDecimal)) $comp = -1;
	
	return $comp;
	
	//return ( floatval($a["Revision"]) < floatval($b["Revision"])) ? -1 : 1;
}


/***
 * Convert line-by-line format into JSON
*/
function lines2JSON($string){
	$json = "[";

	$jsonChangesStrings = explode("\n\n", $string);

	$len = count($jsonChangesStrings);
	for ($i = 0; $i < $len; $i++) {
		$json .= $jsonChangesStrings[$i];

		if ($i < ($len - 2) )
			$json .= ",";
	}

	$json.= "]";

	return $json;
}


/*
 * Calcule the Space Window around an interval and normalizes it (to be included within the pad)
 */
function calculateSpaceWindowCharsPositions($interval, $spaceWindow, $docLength){

	$preL = $interval[0];
	$preR = $interval[1];

	$preLextended = $preL - $spaceWindow;
	$preRextended = $preR + $spaceWindow;

	$charsL = $preLextended - 1;
	$charsR = $preRextended - 2;

	$charsLNormalized = max(0,$charsL);
	$charsRNormalized = min($charsR, $docLength - 1);

	/***
	 echo "\n";
	echo "\nPRE Interval: ".implode($interval,', ');
	echo "\nWindow: ".$spaceWindow;
	echo "\n\$preL: ".$preLextended;
	echo "\n\$preR: ".$preRextended;
	echo "\n\$charsL: ".$charsL;
	echo "\n\$charsR: ".$charsR;
	echo "\n\$charsLNormalized: ".$charsLNormalized;
	echo "\n\$charsRNormalized: ".$charsRNormalized;
	echo "\n";
	***/

	return [$charsLNormalized, $charsRNormalized];
}


/*
 * Check if there is temporal collaboration around a change and in a given time window
 * 		-starts from a given position and goes back in time only (revised after discussion about simmetry between space and time)
*/
function checkTimeCollaboration(&$arrayOfChanges, $editPosition, $timeWindow){

	$mainAuthor = $arrayOfChanges[$editPosition]["Author"];
	$timestampBase = $arrayOfChanges[$editPosition]["Timestamp"];
	
	$foundIt = FALSE;
	$outOfTimeWindow = FALSE;
	
	for ($i = $editPosition; (($i >= 0) && !$foundIt && !$outOfTimeWindow); $i--) {

		$authorBeingChecked = $arrayOfChanges[$i]["Author"];

		$elapsedTime = abs($arrayOfChanges[$i]["Timestamp"] - $timestampBase);
		
		if ($elapsedTime > $timeWindow)
			$outOfTimeWindow = TRUE;
		else
			{
			if (($authorBeingChecked != $mainAuthor) && ($authorBeingChecked != ""))
				$foundIt = TRUE;
			}
	}

	return $foundIt;
}



function checkSpaceCollaboration(&$arrayOfChanges, $i, $spaceWindow, $timeWindow, $coloredPad)
	{
	
	$spaceCollaboration = FALSE;
	$spacetimeCollaboration = FALSE;
		
	$mainAuthor = $arrayOfChanges[$i]["Author"];

	if ($mainAuthor != "") {
			$charsNormalizedInterval = calculateSpaceWindowCharsPositions($arrayOfChanges[$i]["preInterval"], $spaceWindow, $arrayOfChanges[$i]["preDocLength"]);
			
			$charsLNormalized = $charsNormalizedInterval[0];
			$charsRNormalized = $charsNormalizedInterval[1];
			
			for ($j = $charsLNormalized; (($j <= $charsRNormalized) && !$spacetimeCollaboration); $j++) {
				$authorBeingChecked = $arrayOfChanges[ intval($coloredPad[$j]) ]["Author"];
				$timestampBeingChecked = $arrayOfChanges[ intval($coloredPad[$j]) ]["Timestamp"];
				
				if (($authorBeingChecked != $mainAuthor) && ($authorBeingChecked != ""))
					{
						$spaceCollaboration = TRUE;
						
						$inTimeWindow = TRUE;
						
						// interested in spacetime collaboration
						if ($timeWindow != NULL)
							$inTimeWindow = (abs($arrayOfChanges[$i]["Timestamp"] - $timestampBeingChecked) < $timeWindow) ? TRUE:FALSE;
							
						if ($inTimeWindow)
							$spacetimeCollaboration = TRUE;
							
					}

			}
		}
	
		$spaceCollaborationArray = array();
		
		$spaceCollaborationArray["spaceCollaboration"] = $spaceCollaboration;
		$spaceCollaborationArray["spacetimeCollaboration"] = $spacetimeCollaboration;

		return $spaceCollaborationArray;
	}
	
		
	function applyChangeToColoredPad($markedPad, $op, $posInOrderedChanges){
			
		$changeOp = $op["opCode"];
		$touchedCharsLength = $op["touchedCharsLength"];
		$changeStartPos = $op["preInterval"][0] - 1;
			
		$opRefsBeingInserted = generateMultipleOpRefs($posInOrderedChanges, $touchedCharsLength);
	
		switch ($changeOp) {
			case "INS":
				array_splice($markedPad, $changeStartPos, 0, $opRefsBeingInserted);
				break;
			case "DEL":
				array_splice($markedPad, $changeStartPos, $touchedCharsLength);
				break;
			case "UPD":
				array_splice($markedPad, $changeStartPos, $touchedCharsLength, $opRefsBeingInserted);
				break;
			default:
				break;
		}
	
		return $markedPad;
	}
	
	function addTimeCollaborationToJSONArray(&$jsonChanges, $timeWindow) {
	
		for ($i = 0; $i < count($jsonChanges); $i++) {
			
			if ($jsonChanges[$i]["Author"] == "")
				$jsonChanges[$i]["timeCollaboration"] = FALSE;
			else
			{
				$jsonChanges[$i]["timeCollaboration"] = checkTimeCollaboration($jsonChanges, $i, $timeWindow);
			}
		}	
	}
	

	function addSpaceCollaborationToJSONArray(&$jsonChanges, $spaceWindow, $timeWindow = NULL, $historyWorkingDir){
	
		$markedPad = [];
		
		for ($i = 0; $i < count($jsonChanges); $i++) {

			// Serialize on a history file if the option is set and the directory is ok
			if ($historyWorkingDir != NULL)
				{
				$markedPadSerialized = implode(",",$markedPad);
				$markedPadSerializedFilename = $historyWorkingDir . "/" . $jsonChanges[$i]["Revision"]."___MarkedPadStringBefore.txt";
				file_put_contents($markedPadSerializedFilename, $markedPadSerialized);
				}	
			
			
			$spaceCollaboration = checkSpaceCollaboration($jsonChanges, $i, $spaceWindow, $timeWindow, $markedPad);
	
			$jsonChanges[$i]["spaceCollaboration"] = $spaceCollaboration["spaceCollaboration"];
			$jsonChanges[$i]["spacetimeCollaboration"] = $spaceCollaboration["spacetimeCollaboration"];
			
			$markedPad = applyChangeToColoredPad($markedPad, $jsonChanges[$i], $i);
		}
	}
	
	
	
	
	function coloredPad2AuthorsPad($markedPad, &$jsonChanges, $listOfAuthors){

		$total = count($markedPad);
		
		for ($i = 0; $i < $total; $i++) {

			$changeAuthor = $jsonChanges[$markedPad[$i]]["Author"];
			
			$markedPad[$i] = array_search($changeAuthor, $listOfAuthors);
			
		}
		
		return $markedPad;
	}
	
	
	function addAllCollaborationsToJSONArray(&$jsonChanges, $spaceWindow, $timeWindow = NULL, $historyWorkingDir) {
	
		$markedPad = [];
		$listOfAuthors = array();
		
		$totalChanges = count($jsonChanges);
		for ($i = 0; $i < $totalChanges; $i++) {

			//echo "\nInspecting change $i of $totalChanges";
			
			// Serialize on a history file if the option is set and the directory is ok
			if ($historyWorkingDir != NULL)
			{
				
				$candidateAuthor = $jsonChanges[$i]["Author"];
					
				if (!(in_array($candidateAuthor, $listOfAuthors)))
					$listOfAuthors[] = $candidateAuthor;
					
				$authorsPad = coloredPad2AuthorsPad($markedPad, $jsonChanges, $listOfAuthors);
				
				$markedPadSerialized = implode(",", $authorsPad);
				$markedPadSerializedFilename = $historyWorkingDir . "/" . $jsonChanges[$i]["Revision"]."___MarkedPadStringBefore.txt";
				file_put_contents($markedPadSerializedFilename, $markedPadSerialized);
			}
			
			
			if ($jsonChanges[$i]["Author"] == "")
				{
				$jsonChanges[$i]["timeCollaboration"] = FALSE;
				$jsonChanges[$i]["spaceCollaboration"] = FALSE;
				$jsonChanges[$i]["spacetimeCollaboration"] = FALSE;
				}
			else
			{
				$jsonChanges[$i]["timeCollaboration"] = checkTimeCollaboration($jsonChanges, $i, $timeWindow);
				
				$spaceCollaboration = checkSpaceCollaboration($jsonChanges, $i, $spaceWindow, $timeWindow, $markedPad);
				
				$jsonChanges[$i]["spaceCollaboration"] = $spaceCollaboration["spaceCollaboration"];
				$jsonChanges[$i]["spacetimeCollaboration"] = $spaceCollaboration["spacetimeCollaboration"];
					
			}
			
			$markedPad = applyChangeToColoredPad($markedPad, $jsonChanges[$i], $i);
		}
		
		
	}
	
	
	
	function checkIfContainsEditGaps($historyString){
	
		/*
		 * Convert JSON (lines) to array
		*/
		$longJSON = lines2JSON($historyString);
	
		$jsonChanges = json_decode($longJSON, true);
	
		/*
		 * Sort changes by revision number
		*/
		usort($jsonChanges, "compareRevisionNumbers");
	
		$postPreviousEdit = 0;
		foreach ($jsonChanges as $edit) {
	
			$subRev = (int) explode(".",$edit["Revision"])[1];
				
			$preCurrentEdit = (int) $edit["preDocLength"];
				
			if ($preCurrentEdit != $postPreviousEdit)
				return TRUE;
	
			$postPreviousEdit = (int) $edit["postDocLength"];
				
		}
			
		return FALSE;
	}
	
	
?>