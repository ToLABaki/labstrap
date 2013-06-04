BUILD_PATH = build
BOOTSTRAP_BUILD_PATH = ${BUILD_PATH}/bootstrap
NODE_MODULE_BIN = node_modules/.bin
MISC = \
	${BUILD_PATH}/*.md \
	${BUILD_PATH}/LICENSE \
	${BOOTSTRAP_BUILD_PATH}/LICENSE \
	${BUILD_PATH}/images/* \
	${BOOTSTRAP_BUILD_PATH}/awesome/font/*

.PHONY: build watch misc php css js clean

build: css js php misc

#
# Move misc files to build
#

misc: ${MISC}

${MISC}: *.md LICENSE bootstrap/LICENSE images/* font-awesome/font/*
# doc
	cp README.md ${BUILD_PATH}
	cp README.strapping.md ${BUILD_PATH}
	cp LICENSE ${BUILD_PATH}
	cp bootstrap/LICENSE ${BOOTSTRAP_BUILD_PATH}
	
# images
	cp -r images ${BUILD_PATH}

# font-awesome
	mkdir -p ${BOOTSTRAP_BUILD_PATH}/awesome
	cp -r font-awesome/font ${BOOTSTRAP_BUILD_PATH}/awesome

#
# Move php files to build
#

php: ${BUILD_PATH}/*.php

${BUILD_PATH}/*.php: php/*.php
	mkdir -p ${BUILD_PATH}
	cp php/*.php ${BUILD_PATH}

#
# Compile css
#

css: ${BUILD_PATH}/css/labstrap.css ${BOOTSTRAP_BUILD_PATH}/css/*.css

# labstrap css

${BUILD_PATH}/css/labstrap.css: less/*.less
	mkdir -p ${BUILD_PATH}/css
	./${NODE_MODULE_BIN}/lessc less/labstrap.less > ${BUILD_PATH}/css/labstrap.css

# bootstrap css

${BOOTSTRAP_BUILD_PATH}/css/*.css: less/bootstrap/*.less
	mkdir -p ${BOOTSTRAP_BUILD_PATH}/css
	./${NODE_MODULE_BIN}/lessc less/bootstrap/swatch.less > ${BOOTSTRAP_BUILD_PATH}/css/bootstrap.css
	./${NODE_MODULE_BIN}/lessc --yui-compress less/bootstrap/swatch.less > ${BOOTSTRAP_BUILD_PATH}/css/bootstrap.min.css
	./${NODE_MODULE_BIN}/lessc less/bootstrap/swatch-responsive.less > ${BOOTSTRAP_BUILD_PATH}/css/bootstrap-responsive.css
	./${NODE_MODULE_BIN}/lessc --yui-compress less/bootstrap/swatch-responsive.less > ${BOOTSTRAP_BUILD_PATH}/css/bootstrap-responsive.min.css

#
# js
#

js: ${BUILD_PATH}/js/*.js ${BOOTSTRAP_BUILD_PATH}/js/*.js

# labstrap js

${BUILD_PATH}/js/*.js: js/*.js js/*.htc
	mkdir -p ${BUILD_PATH}/js
	cp js/*.js ${BUILD_PATH}/js
	cp js/*.htc ${BUILD_PATH}/js

# bootstrap js

${BOOTSTRAP_BUILD_PATH}/js/*.js: bootstrap/js/*.js
	mkdir -p ${BOOTSTRAP_BUILD_PATH}/js
	cat bootstrap/js/bootstrap-transition.js bootstrap/js/bootstrap-alert.js bootstrap/js/bootstrap-button.js bootstrap/js/bootstrap-carousel.js bootstrap/js/bootstrap-collapse.js bootstrap/js/bootstrap-dropdown.js bootstrap/js/bootstrap-modal.js bootstrap/js/bootstrap-tooltip.js bootstrap/js/bootstrap-popover.js bootstrap/js/bootstrap-scrollspy.js bootstrap/js/bootstrap-tab.js bootstrap/js/bootstrap-typeahead.js bootstrap/js/bootstrap-affix.js > ${BOOTSTRAP_BUILD_PATH}/js/bootstrap.js
	./${NODE_MODULE_BIN}/uglifyjs -nc ${BOOTSTRAP_BUILD_PATH}/js/bootstrap.js > ${BOOTSTRAP_BUILD_PATH}/js/bootstrap.min.tmp.js
	echo "/*!\n* Bootstrap.js by @fat & @mdo\n* Copyright 2012 Twitter, Inc.\n* http://www.apache.org/licenses/LICENSE-2.0.txt\n*/" > ${BOOTSTRAP_BUILD_PATH}/js/copyright.js
	cat ${BOOTSTRAP_BUILD_PATH}/js/copyright.js ${BOOTSTRAP_BUILD_PATH}/js/bootstrap.min.tmp.js > ${BOOTSTRAP_BUILD_PATH}/js/bootstrap.min.js
	rm ${BOOTSTRAP_BUILD_PATH}/js/copyright.js ${BOOTSTRAP_BUILD_PATH}/js/bootstrap.min.tmp.js

# watch labstrap less files

watch:
	watchr watch.rb

clean:
	rm -r ${BUILD_PATH}