# Purpose
The goal of this software is to analyze the real-time collaborative editing of an existing installation of Etherpad.

# Scope
This repository contains all the code used for the experiments in the paper "Spacetime Characterization of Real-Time Collaborative Editing" (Gabriele D'Angelo, Angelo Di Iorio, Stefano~Zacchiroli).  This paper is available in the ``paper`` folder.

# Install dependencies
```
sudo apt install python3-mysqldb python-sqlalchemy python3-dev default-libmysqlclient-dev build-essential php
npm install fast-csv graceful-fs md5
```

# Usage

## Dump
Dump (and anonymize) the Etherpad database.

```
chmod +x db-dumper/etherpad-anon-dump
./db-dumper/etherpad-anon-dump mysql://<user>:<passwd>@<dbhost>/<dbname> > dump.sql
```

## Analysis

```
chmod +x main.sh
chmod +x inspector/analyze_pads.sh
./main.sh
```

All the runtime parameters can be found in the "configuration.sh" script.  

Please note, the DB_FILE must be changed to point to the <db_dump_file> obtained above.

# Why so many languages?!
* Bash script used for init
* Python used for MySQL export and anonymization of pad and author data
* JS used for putting revs/cs/author into JSON blobs?
* PHP used for:
  * removing pads that can't be processed properly
  * checking if pad has "gaps" in revs
  * gathering stats and printing them out

# License
GNU GENERAL PUBLIC LICENSE
