const ChangesetPreprocessor = require(__dirname + '/lib/changeset/ChangesetPreprocessor');
const fs = require('graceful-fs');
const md5 = require('md5');
var parseStream = require('fast-csv').parseStream;

dumpFile = process.argv[2];
outputDir = process.argv[3];
emptyAuthorDir = process.argv[4];

if( (realPath=getFileRealPath(dumpFile)) === false) showMsgCommandAndExit("ERROR: dump file does not exist")
if( (realPath=getFileRealPath(outputDir)) === false) showMsgCommandAndExit("ERROR: output directory does not exist")

const stream = fs.createReadStream(dumpFile);

parseStream(stream, { delimiter: '\t', objectMode: true})
	.on('data', function (line) {
    		var key = line[0];
		var value = line[1];

		var regexrevs = /pad:(.+):revs:[0-9]+$/;
		// NOTE: if you want to extract just one pad use the following syntax:
		// var regexrevs = /pad:(7PsM73Ah3L):revs:[0-9]+$/;
		if (regexrevs.test(key)) {
		    padID = key.substring(4,key.indexOf(":revs:"));
		    revID = key.substring(key.indexOf(":revs:") + 6);

		    console.log(md5(padID));
		    // TOBEREMOVED
		    // Save extracted pad (with no processing, just raw data)
		    //fs.appendFile( outputDir + "RTCE_EXTRACTEDPAD_" + padID + ".txt" , key + "\t" + value + "\n");
		    rev = JSON.parse(value);
		    cs = rev.changeset;
		    author = rev.meta.author;

			try {
				changes = ChangesetPreprocessor.changeset2json(cs, padID, revID);
				jsonPadFilepath = outputDir + padID + ".txt";
				for (var c in changes) {
					fs.appendFile( jsonPadFilepath, JSON.stringify(changes[c]) + "\n\n",  encoding='utf8', function (err) {
						if (err) {
							console.log("\nError in append changes to pad.")
							fs.appendFile( outputDir + "RTCE_PADS-BLACKLIST.txt", md5(padID) + "\n");
							}
					});
					console.log("Added change to: " + jsonPadFilepath + "\n");
					}
				}
			catch (ex) {
				fs.appendFile( outputDir + "RTCE_PADS-BLACKLIST.txt", md5(padID) + "\n");
			}
		}
});








function getFileRealPath(s){
    try {return fs.realpathSync(s);} catch(e){return false;}
}

function showMsgCommandAndExit(c){
	console.log("\n" + c)
	console.log("\nCOMMAND: node.js dump2padfiles.js <dump> <output_dir> [<empty_authors_dir>]\n")
	process.exit(1);
}


