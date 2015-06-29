#!/usr/bin/env bash

chmod -R 755 sites/default/files
chmod -R 755 sites/default/OSCI-Toolkit-Frontend
# symlink OSCI-Toolkit-Frontend into frontend
mkdir -p frontend
cd frontend
ln -s ../sites/default/OSCI-Toolkit-Frontend
cd ..
# symlink OSCI-Toolkit-Frontend into libraries
mkdir -p sites/all/libraries
cd sites/all/libraries
ln -s ../../default/OSCI-Toolkit-Frontend
cd ../../..
# symlink OSCI-Toolkit into modules
if [ ! -d sites/default/modules ]; then
    mkdir -p sites/default/modules
fi
cd sites/default/modules
ln -s ../OSCI-Toolkit/modules/* .
