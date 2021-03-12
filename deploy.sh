#!/bin/bash

echo "Preparing Moloni Component..."
# shellcheck disable=SC2035
cd com_moloni/ && zip -r ../com_moloni.zip * && cd ..

echo "Preparing Moloni Plugin..."
# shellcheck disable=SC2035
cd plg_moloni/ && zip -r ../plg_moloni.zip * && cd ..

echo "Packing everything up..."
zip pkg_moloni.zip com_moloni.zip plg_moloni.zip pkg_moloni.xml

echo "Deleting temporary files..."
rm com_moloni.zip
rm plg_moloni.zip

echo "Process complete!"

sleep 5s