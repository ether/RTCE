var Changeset = require('./Changeset');

cs = process.argv[2];

/**
cs = "Z:g<6-7*1+1$R";
cs = "Z:a>4=1*1+4$enzi";
cs = "Z:4jf>0|1e=1v8*3|d=o5$";
//cs = "Z:7n>1|g=7l=1|1+1$\\p";
//cs = "Z:4>1|1=2=1|1+1$\\m";
//cs = "Z:ln>1|s=ll=1|1+1$\\h";
//cs = "Z:uh>1|11=uf=1|1+1$\\r";
cs = "Z:4jh>0|17=1md*3|1=18$";
cs = "Z:4jh>0|1e=1v8*5|c=lu=1u*5=1*6=1*3=1$";
**/

var unpacked = Changeset.unpack(cs);

console.log()
console.log(cs);
console.log()
console.log(unpacked);

var opiterator = Changeset.opIterator(unpacked.ops);


while (opiterator.hasNext()) {
	
		ops = opiterator.next();
		
		console.log(ops);	
}

console.log();