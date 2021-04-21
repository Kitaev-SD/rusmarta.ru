#!/bin/bash

# converting JPEG images to WebP
find /var/www/rusmarta.ru/upload/medialibrary/ -type f -and \( -iname "*.jpg" -o -iname "*.jpeg" \) \
-exec bash -c '
webp_path=$0.webp;
if [ ! -f "$webp_path" ]; then
cwebp -quiet -q 90 "$0" -o "$webp_path";
echo $0;
fi;' {} \;

# converting PNG images to WebP
find /var/www/rusmarta.ru/upload/medialibrary/ -type f -and -iname "*.png" \
-exec bash -c '
webp_path=$0.webp;
if [ ! -f "$webp_path" ]; then
cwebp -quiet -lossless "$0" -o "$webp_path";
echo $0;
fi;' {} \;