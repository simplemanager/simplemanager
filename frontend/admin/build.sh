#/bin/sh

npm run build
rm -rf ../../htdocs/www/static/*
cp -r dist/static ../../htdocs/www/
cp dist/index.html ../../htdocs/www/layout.html
