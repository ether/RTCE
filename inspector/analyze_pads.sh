#!/bin/bash

# Copyright (C) 2016    Gabriele D'Angelo <g.dangelo@unibo.it>
# License: GNU General Public License version 3, or any later version

#
# Master script for the analysis of pads
#

# Including some configuration parameters
source configuration.sh

# For each space and time window configuration
for time_size in $TIME_WIN_SIZE
do
	for space_size in $SPACE_WIN_SIZE
	do
		echo "case: TIME_WIN_SIZE $time_size, SPACE_WIN_SIZE $space_size"
		OUTPUT="output-TIME_$time_size-SPACE_$space_size.dat"
		touch $OUTPUT
		# Parallel processing of the pads
		find $PADS_DIR -name "*.txt" | tac | parallel -j $NUM_PROC ./inspector/analyze_pads-atom.sh "{}" $space_size $time_size $OUTPUT
	done
done

cd ..
