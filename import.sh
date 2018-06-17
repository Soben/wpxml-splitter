#!/bin/bash

# Requires WP CLI

# Change this to your output path
FILES="./path/to/batch/files/*.xml"

for f in $FILES
do
  echo "Processing $f file..."
    # take action on each file. $f store current file name
    wp import $f --authors=skip --skip=image_resize
done