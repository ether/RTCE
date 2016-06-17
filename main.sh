#!/bin/bash

# Copyright (C) 2016  	Gabriele D'Angelo <g.dangelo@unibo.it>
#			Angelo di Iorio <angelo.diiorio@unibo.it>
# License: GNU General Public License version 3, or any later version

#
# Master script for the cleaning and analysis of pads
#

# Including some configuration parameters
source configuration.sh

mkdir -p $TMP_DIR "$TMP_DIR/PADS" $RESULTS

# Removing the pads that can not be parsed correctly
php preprocessor/remove_unparsable.php $DB_FILE $TMP_DIR
# Re-building of each single pad starting from the DB dump 
node inspector/dump2padfiles.js "$TMP_DIR/RTCE_PARSABLE_DUMP.txt" "$PADS_DIR/"
# Removing the pads that have gaps
php inspector/edit_gaps_detector.php $PADS_DIR -md5 >> $FILTERFILE
# (Parallel) analysis of each single pad
./inspector/analyze_pads.sh

mv output-*.dat	$RESULTS

# Cleaning
rm -fr $TMP_DIR
