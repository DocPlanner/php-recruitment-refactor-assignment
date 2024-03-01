#!/usr/bin/env sh

ARCH=$(uname -m)

if [ "x86_64" == "$ARCH" ]; then
  exec bin/VendorApi_linux_x64
else
  # run ARM - Docker on M1/M2
  exec bin/VendorApi_linux
fi
