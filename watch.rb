watch('less/.*\.less') { system 'make css' }
watch('less/bootstrap/.*\.less') { system 'make css' }
watch('php/.*\.php') { system 'make php' }
watch('js/.*') { system 'make js' }
# watch('bootstrap/js/.*') { system 'make js' }