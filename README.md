# Labstrap

A mediawiki theme built for the tolabaki hackerspace, based on the [strapping](https://github.com/OSAS/strapping-mediawiki) theme.

## Building

### Dependencies

* Node.js
* npm
* grunt-cli

### Build

Run `npm install` to install the node modules needed to build labstrap

* run `grunt` or `grunt dist-mediawiki` to build labstrap inside the _dist-mediawiki_ folder
* run `grunt dist-bootstrap` to build a standalone bootstrap redistributable using labstrap inside the _dist-bootstrap_ folder

### Other build targets

* `watch` watches labstrap php scripts, less stylesheets and javascript scripts for modifications and runs the applicable build targets
* `clean` cleans up _dist-mediawiki_ and _dist-bootstrap_

