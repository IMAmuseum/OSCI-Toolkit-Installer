#!/usr/bin/env bash

function usage() {
    echo "Usage: move-dependencies /path/to/drupal"
    echo ""
}

# test for arguments
if [ ! $1 ] || [ ! -d $1 ]; then
    usage
    exit
fi

# test for index.php - to ensure a drupal directory
if [ ! -f $1/index.php ]; then
    echo "Error: This doesn't look like a Drupal install (no index.php)"
    echo $1/index.php
    echo
    usage
    exit
fi

# test for trailing slash - should not be present
dpath=$(echo $1 | awk '{print substr($0,length,1)}')
if [ $dpath = '/' ]; then
    echo "Error: No trailing slash '/' on path please"
    echo
    usage
    exit
fi

# if no src dir, create one
if [ ! -d ./src ]; then
    mkdir ./src
fi

# store current directory
cpath=`pwd`

echo

#
# Gathering OSCI-Toolkit-Frontend
#
echo "symlink OSCI-Toolkit-Frontend"
# cd $1/sites/default
# git clone -v https://github.com/IMAmuseum/OSCI-Toolkit-Frontend.git
cd $1
ln -s sites/default/OSCI-Toolkit-Frontend frontend
cd $cpath
mkdir -p $1/sites/all/libraries
cd $1/sites/all/libraries
ln -s ../../default/OSCI-Toolkit-Frontend
cd $cpath
echo


#
# Create module symlinks
#
echo "Creating module symlinks..."
if [ ! -d $/sites/default/modules ]; then
    mkdir -p $1/sites/default/modules
fi
cd $1/sites/default/modules
ln -s ../OSCI-Toolkit/modules/* .
cd $cpath
echo


rm -R ./src

echo "Finished"
