# labstrap css

labstrap_css: css/*.css

css/*.css: less/*.less
	lessc less/labstrap.less css/labstrap.css
	
# watch labstrap less files

watch:
	echo "Watching less files..."; \
	watchr -e "watch('less/.*\.less') { system 'make labstrap_css' }"


.PHONY: watch labstrap_css