#!/bin/bash -eufx

UPLOAD_USER="%%%LIBNAPC_DEPLOY_USER%%%"
VERSION="%%%LIBNAPC_RELEASE_VERSION%%%"

cd /home/$UPLOAD_USER/www/
rm -rf v$VERSION/
rm -rf v$VERSION.tmp/
mkdir v$VERSION.tmp/
cd v$VERSION.tmp/

mv ../../tmp/libnapc-documentation-v$VERSION.tar.gz .
mv ../../tmp/libnapc-linux-v$VERSION.tar.gz .
mv ../../tmp/libnapc-arduino-v$VERSION.zip .
mv ../../tmp/libnapc-v$VERSION.h .
mv ../../tmp/check-integrity-v$VERSION.sh .

chmod +x check-integrity-v$VERSION.sh

./check-integrity-v$VERSION.sh
rm ./check-integrity-v$VERSION.sh

tar -xzvf libnapc-documentation-v$VERSION.tar.gz -C .

mkdir download

mv libnapc-linux-v$VERSION.tar.gz download/libnapc-linux.tar.gz
mv libnapc-arduino-v$VERSION.zip download/libnapc-arduino.zip
mv libnapc-v$VERSION.h download/napc.h

rm libnapc-documentation-v$VERSION.tar.gz

cd ..

mv v$VERSION.tmp v$VERSION

rm -f symlink-to-latest-version
ln -s v$VERSION symlink-to-latest-version

rm ../tmp/install-v$VERSION.sh
