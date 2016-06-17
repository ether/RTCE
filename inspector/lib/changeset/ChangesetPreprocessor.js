var Changeset = require('./Changeset');
var AttributePool = require("./AttributePool");

exports.changeset2json = function (cs, $padID, revID){
	
    var unpacked = Changeset.unpack(cs);

    var opiterator = Changeset.opIterator(unpacked.ops);

    var currentPosition = 1;
    var currentDocLength = unpacked.oldLen;

    var expandedChanges = [];
    var subChangeIndex = 0;
    

    while (opiterator.hasNext()) {
    	
    		ops = opiterator.next();
    		
    		preDocLength = currentDocLength;		
    		touchedCharsLength = ops.chars;
    			
    		addToLogs = true;
    		
    		switch(ops.opcode) {
    	    case "=":
    	    	if (ops.attribs == "")
    	    		{
    	    		opCode = "CPY";
    	    		addToLogs = false;
    	    		}
    	    	else
    	    		opCode = "UPD";
    	    		
    	    	newPosition = currentPosition + ops.chars;
    	    	preInterval = [currentPosition, newPosition];
    	    	postInterval = [currentPosition, newPosition];
    	    	currentPosition = newPosition;
    	    		
    	    	postDocLength = preDocLength;
    	    	addedContent = "";
    	    	break;
    	    case "+":
    	    	opCode = "INS";
        		newPosition = currentPosition + ops.chars;
        		preInterval = [currentPosition, currentPosition];
	    		postInterval = [currentPosition, newPosition];
        		currentPosition = newPosition;
        		postDocLength = preDocLength + ops.chars;
        		addedContent = unpacked.charBank;
        		break;
    	    case "-":
    	    	opCode = "DEL";
    	    	preInterval = [currentPosition, currentPosition + ops.chars];
        		postInterval = [currentPosition, currentPosition];
        		postDocLength = preDocLength - ops.chars;
        		addedContent = "";
        		break;
    	    default:
    	    }
    		
    		currentDocLength = postDocLength;
    		
    		revisionFullID = revID + "." + subChangeIndex;	    		
    			 
    		change = {
    				"Pad" : padID,
    	    		"Revision": revisionFullID,
    	    		"Author" : rev.meta.author,
    	    		"Timestamp": rev.meta.timestamp,
    	    		"opCode" : opCode,
    				"preDocLength" : preDocLength - 1,
    				"postDocLength" : postDocLength - 1,
    				"touchedCharsLength" : touchedCharsLength,
    				//"addedContent" : addedContent,
    				"preInterval": preInterval,
    				"postInterval": postInterval
    				};
    		
    		if (addToLogs)
				{
    			subChangeIndex += 1;
    			expandedChanges.push(change);
				}
    }
    
	return expandedChanges;
}