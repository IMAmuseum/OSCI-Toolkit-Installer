#!/usr/bin/env bash

chmod -R 755 sites/default/files
chmod -R 755 sites/default/OSCI-Toolkit-Frontend
cd osci-assets
bash move-dependencies.sh ..
