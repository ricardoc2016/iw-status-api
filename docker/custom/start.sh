#!/usr/bin/env bash

echo "Starting IW Status API Environment...";

echo "Executing install.sh";

/development/install.sh;

echo "Starting Apache...";

service apache2 start;

tail -F /development/var/logs/dev.log; /development/var/logs/prod.log;