This repository contains all the code used for the experiments in the
paper "Spacetime Characterization of Real-Time Collaborative Editing"
(Gabriele D'Angelo, Angelo Di Iorio, Stefano~Zacchiroli).

### node.js dependecies: fast-csv  graceful-fs  md5
### npm install fast-csv graceful-fs md5

The goal of this software is to analyze the real-time collaborative
editing of an existing installation of Etherpad Lite. 

The first operation to be done is the dump (and anonymization) of the 
Etherpad database. This can be done using the "etherpad-anon-dump"
script in the "db-dumper/" directory. The correct syntax to be used 
is reported in the following.
EXAMPLE: ./etherpad-anon-dump mysql://<user>:<passwd>@<dbhost>/<dbname>    >   <db_dump_file>

The analysis of the dump is performed by the the "main.sh" script that
can be found in the main directory of this repository.
EXAMPLE: ./main.sh

All the runtime parameters can be found in the "configuration.sh" script.
Please note, the DB_FILE must be changed to point to the <db_dump_file>
obtained above.

