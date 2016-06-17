<?php

/*
 * Count edits: total, marked as space/time collaborative or both
 */
function getEditStats($jsonChanges){
	
	$stats["total"] = 0;
	$stats["space"] = 0;
	$stats["time"] = 0;
	$stats["spacetime"] = 0;
	
	
	$listOfAuthors = array();
	$listOfAuthorsTimeCollaboration = array();
	$listOfAuthorsSpaceCollaboration = array();
	$listOfAuthorsSpaceTimeCollaboration = array();
	
	
	$sumPadsLengths = 0;
	
	$cjChanges = count($jsonChanges);
	
	for ($i = 0; $i < $cjChanges; $i++) {
		
		$currentAuthor = $jsonChanges[$i]["Author"];

		// TODO: do we count edits with empty author here?
		$sumPadsLengths += $jsonChanges[$i]["postDocLength"];
		
		if ($currentAuthor != "")
			{
		
			$stats["total"] += 1;
			
			if (!($listOfAuthors[$currentAuthor] === TRUE))
				$listOfAuthors[$currentAuthor] = TRUE;
			
			if ($jsonChanges[$i]["timeCollaboration"])
				{
					$stats["time"] += 1;
					
					if (!($listOfAuthorsTimeCollaboration[$currentAuthor] === TRUE))
						$listOfAuthorsTimeCollaboration[$currentAuthor] = TRUE;
				}
			
			if ($jsonChanges[$i]["spaceCollaboration"])
				{
					$stats["space"] += 1;

					if (!($listOfAuthorsSpaceCollaboration[$currentAuthor] === TRUE))
						$listOfAuthorsSpaceCollaboration[$currentAuthor] = TRUE;

				}
			
			if ($jsonChanges[$i]["spacetimeCollaboration"])
				{
					$stats["spacetime"] += 1;
					
					if (!($listOfAuthorsSpaceTimeCollaboration[$currentAuthor] === TRUE))
						$listOfAuthorsSpaceTimeCollaboration[$currentAuthor] = TRUE;
				}
			}
	}

	$stats["avg-doc-length"] = floor($sumPadsLengths / $cjChanges);
	$stats["authors_total"] = sizeof($listOfAuthors);
	$stats["authors_space"] = sizeof($listOfAuthorsSpaceCollaboration);
	$stats["authors_time"] = sizeof($listOfAuthorsTimeCollaboration);
	$stats["authors_spacetime"] = sizeof($listOfAuthorsSpaceTimeCollaboration);
	
	return $stats;
}


?>