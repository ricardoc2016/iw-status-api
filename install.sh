#!/usr/bin/env bash

##################################################################
# Installation
##################################################################

STA_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

if [ ! -f "${STA_DIR}/install_params.sh" ] ; then
    echo "File ${STA_DIR}/install_params.sh not found. Using .dist file by default...";

    cp "${STA_DIR}/install_params.sh.dist" "${STA_DIR}/install_params.sh";
fi

. "${STA_DIR}/install_params.sh";

echo "Creating symlink on Apache htdocs dir...";

if [ -L "${STA_APACHE_SYMLINK_TARGET}" ] ; then
    unlink "${STA_APACHE_SYMLINK_TARGET}";
fi

ln -s "${STA_DIR}/web" "${STA_APACHE_SYMLINK_TARGET}";

if [ "$?" != "0" ] ; then
    echo "There was a problem while trying to create the symlink to our web directory. Please, make sure path configured on env variable STA_APACHE_SYMLINK_TARGET (${STA_APACHE_SYMLINK_TARGET}) does NOT exist.";

    exit 1;
fi

echo "Removing Cache...";

rm -rf "${STA_DIR}/var/cache/*";

echo "Setting permissions on Cache, logs and sessions directories...";

chown -R "${STA_APACHE_USER}:${STA_APACHE_GROUP}" "${STA_DIR}/var/cache" "${STA_DIR}/var/logs" "${STA_DIR}/var/sessions"

echo "Publishing Assets...";

"${STA_DIR}/bin/console" assets:install "${STA_DIR}/web" --symlink;

echo "Executing final Installation tasks...";

"${STA_DIR}/bin/console" sta:install;