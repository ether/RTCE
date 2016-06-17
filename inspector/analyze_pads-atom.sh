#!/bin/bash

# Copyright (C) 2016    Gabriele D'Angelo <g.dangelo@unibo.it>
# License: GNU General Public License version 3, or any later version

#
# Script called for the (parallel) processing of each pad
#

# Including some configuration parameters
source configuration.sh

FILENAME=$(basename "$1")
FILENAME="${FILENAME%.*}"

# Blacklisted pads can be skipped
FILEMD5=`echo -n "$FILENAME" | md5sum | awk '{print $1}'`
grep --silent $FILEMD5 $FILTERFILE
if [ $? -eq 1 ]
then
	# not blacklisted, processing...
	echo -e "\tprocessing\t$2$FILENAME"
	php inspector/singlepad2inspectedcollaboration.php "$1" $2 $3 >> $4
else
	# blacklisted, to be skipped...
	echo -e "\tNOT processing\t$2$FILENAME"
fi
