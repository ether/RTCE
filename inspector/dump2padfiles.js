var ChangesetPreprocessor = require(__dirname + '/lib/changeset/ChangesetPreprocessor');

var fs = require('graceful-fs');
var md5 = require('md5');

dumpFile = process.argv[2];
outputDir = process.argv[3];
emptyAuthorDir = process.argv[4];

function getFileRealPath(s){
    try {return fs.realpathSync(s);} catch(e){return false;}
}

function showMsgCommandAndExit(c){
	console.log("\n" + c)
	console.log("\nCOMMAND: node.js dump2padfiles.js <dump> <output_dir> [<empty_authors_dir>]\n")
	process.exit(1);
}


if( (realPath=getFileRealPath(dumpFile)) === false)
	showMsgCommandAndExit("ERROR: dump file does not exist")

if( (realPath=getFileRealPath(outputDir)) === false)
	showMsgCommandAndExit("ERROR: output directory does not exist")
    

var csv = require('fast-csv');

var fstream = fs.createReadStream(dumpFile),
parser = csv({ delimiter: '\t', objectMode: true});

parser.on('data', function (line) {
	
		var key = line[0];
		var value = line[1];

		var regexrevs = /pad:(.+):revs:[0-9]+$/;
		
		// NOTE: if you want to extract just one pad use the following syntax:
		// var regexrevs = /pad:(7PsM73Ah3L):revs:[0-9]+$/;
		
		if (regexrevs.test(key)) {
			
		    padID = key.substring(4,key.indexOf(":revs:"));
		    revID = key.substring(key.indexOf(":revs:") + 6);

		    console.log(md5(padID));
//		    console.log(key + "\t"  + value + "\n");
		    
		    // TOBEREMOVED
		    // Save extracted pad (with no processing, just raw data)
		    //fs.appendFile( outputDir + "RTCE_EXTRACTEDPAD_" + padID + ".txt" , key + "\t" + value + "\n");
		    
		    rev = JSON.parse(value);
		    cs = rev.changeset;
		    author = rev.meta.author;

			try {
				
				changes = ChangesetPreprocessor.changeset2json(cs, padID, revID);
				
				/***
				if ((!author) && (changes.length > 0) && (revID != 0))
					{
					fs.appendFile( emptyAuthorDir + padID + ".txt" , padID + "\t" + value + "\n");
					}
				else
					{
					if (changes.length == 0)
						{
//						console.log("NO changes found in " + padID)
						fs.appendFile( outputDir + "RTCE_UNSERIALIZEDCHANGES.txt" , padID + "\t" + value + "\n");
						}
					}
				
				***/
					
				jsonPadFilepath = outputDir + padID + ".txt";
					
				for (var c in changes) {
					fs.appendFile( jsonPadFilepath, JSON.stringify(changes[c]) + "\n\n",  encoding='utf8', function (err) {
						if (err) {
							console.log("\nError in append changes to pad.")
							fs.appendFile( outputDir + "RTCE_PADS-BLACKLIST.txt", md5(padID) + "\n");
							}
					});
//					console.log("Added change to: " + jsonPadFilepath + "\n");
					}
				}
			catch (ex) {
				fs.appendFile( outputDir + "RTCE_PADS-BLACKLIST.txt", md5(padID) + "\n");
			}
			
		}	
		
});


fstream.pipe(parser);