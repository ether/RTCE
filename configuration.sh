# Copyright (C) 2016    Gabriele D'Angelo <g.dangelo@unibo.it>
# License: GNU General Public License version 3, or any later version

#
# Configuration values for the pads analysis
#

# Location of the anonymized database dump (db-dumper/)
DB_FILE="/srv/angelo/etherpad-wikimedia-anon-dump"

# Results directory
RESULTS="results/"

# Temporary location for data processing
# WARNING: a large amount of free space might be necessary
TMP_DIR="/tmp/rtce-tmp"

# Location of the pads dumps used during the processing
PADS_DIR="$TMP_DIR/PADS"

# Pads that have to be filetered
FILTERFILE="$TMP_DIR/RTCE_PADS-BLACKLIST.txt"

# Time windows
TIME_WIN_SIZE="5 10 60"

# Space windows
SPACE_WIN_SIZE="10 80 400 800"

# Number of parallel instances of the script to be executed
SOCKETS=`lscpu | grep "Socket(s)" | cut -d":" -f2 | cut -f14 -d" "`
COREPERSOCKET=`lscpu | grep "Core(s) per socket" | cut -d":" -f2 | cut -f5 -d" "`
CPUNUM=`echo $SOCKETS \\* $COREPERSOCKET | bc`
NUM_PROC=$CPUNUM
