/*jslint node: true */

module.exports = function (grunt) {
    var mediawikiBuildPath = "dist-mediawiki/",
        bootstrapMediawikiBuildPath = mediawikiBuildPath + "bootstrap/",
        bootstrapStandaloneBuildPath = "dist-bootstrap/";

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        concat: {
            bootstrapMediawikiJs: {
                src: [
                    "bootstrap/js/transition.js",
                    "bootstrap/js/collapse.js",
                    "bootstrap/js/dropdown.js",
                    "js/bootstrap/slide.js"
                ],
                dest: bootstrapMediawikiBuildPath + "js/bootstrap.js"
            },

            bootstrapStandaloneJs: {
                src: [
                    "bootstrap/js/transition.js",
                    "bootstrap/js/alert.js",
                    "bootstrap/js/button.js",
                    "bootstrap/js/carousel.js",
                    "bootstrap/js/collapse.js",
                    "bootstrap/js/dropdown.js",
                    "bootstrap/js/modal.js",
                    "bootstrap/js/tooltip.js",
                    "bootstrap/js/popover.js",
                    "bootstrap/js/scrollspy.js",
                    "bootstrap/js/tab.js",
                    "bootstrap/js/affix.js"
                ],
                dest: bootstrapStandaloneBuildPath + "js/bootstrap.js"
            }
        },

        uglify: {
            options: {
                bootstrapMediawikiBuildPath: bootstrapMediawikiBuildPath,
                bootstrapStandaloneBuildPath: bootstrapStandaloneBuildPath,
                verbose: true
            },

            bootstrapMediawikiJs: {
                files: {
                    "<%= uglify.options.bootstrapMediawikiBuildPath + 'js/bootstrap.min.js' %>": ["<%= concat.bootstrapMediawikiJs.dest %>"]
                }
            },

            bootstrapStandaloneJs: {
                files: {
                    "<%= uglify.options.bootstrapStandaloneBuildPath + 'js/bootstrap.min.js' %>": ["<%= concat.bootstrapStandaloneJs.dest %>"]
                }
            }
        },

        recess: {
            options: {
                compile: true
            },

            bootstrapMediawikiCss: {
                src: ["less/bootstrap/bootstrap-mediawiki.less"],
                dest: bootstrapMediawikiBuildPath + "css/bootstrap.css"
            },
            bootstrapMediawikiMinCss: {
                options: {
                    compress: true
                },
                src: ["less/bootstrap/bootstrap-mediawiki.less"],
                dest: bootstrapMediawikiBuildPath + "css/bootstrap.min.css"
            },

            bootstrapStandaloneCss: {
                src: ["less/bootstrap/bootstrap.less"],
                dest: bootstrapStandaloneBuildPath + "css/bootstrap.css"
            },
            bootstrapStandaloneMinCss: {
                options: {
                    compress: true
                },
                src: ["less/bootstrap/bootstrap.less"],
                dest: bootstrapStandaloneBuildPath + "css/bootstrap.min.css"
            },

            labstrapCss: {
                src: ["less/labstrap.less"],
                dest: mediawikiBuildPath + "css/labstrap.css"
            },
            labstrapMinCss: {
                options: {
                    compress: true
                },
                src: ["less/labstrap.less"],
                dest: mediawikiBuildPath + "css/labstrap.min.css"
            },
        },

        copyto: {
            mediawikiFonts: {
                files: [
                    {cwd: "fonts/", src: ["**/*"], dest: mediawikiBuildPath + "fonts/"}
                ]
            },
            mediawikiJs: {
                files: [
                    {cwd: "js/", src: ["labstrap.js", "csshover.htc"], dest: mediawikiBuildPath + "js/"}
                ]
            },
            mediawikiPhp: {
                files: [
                    {cwd: "php/", src: ["*.php"], dest: mediawikiBuildPath}
                ]
            },
            mediawikiFontAwesome: {
                files: [
                    {cwd: "font-awesome/css/", src: ["font-awesome.min.css", "font-awesome.css"], dest: mediawikiBuildPath + "css/"},
                    {cwd: "font-awesome/fonts/", src: ["**/*"], dest: mediawikiBuildPath + "fonts/"}
                ]
            },
            bootstrapFonts: {
                files: [
                    {cwd: "bootstrap/fonts/", src: ["**/*"], dest: bootstrapStandaloneBuildPath + "fonts/"}
                ]
            },
            bootstrapExample: {
                files: [
                    {cwd: "assets/", src: ["labstrap.html"], dest: bootstrapStandaloneBuildPath}
                ]
            }
        },

        watch: {
            bootstrapMediawikiLess: {
                files: [
                    "less/bootstrap/*.less"
                ],
                tasks: [
                    "recess:bootstrapMediawikiCss",
                    "recess:bootstrapMediawikiMinCss"
                ]
            },
            labstrapLess: {
                files: [
                    "less/*.less"
                ],
                tasks: [
                    "recess:labstrapCss",
                    "recess:labstrapMinCss"
                ]
            },
            php: {
                files: [
                    "php/*.php"
                ],
                tasks: [
                    "copyto:mediawikiPhp"
                ]
            },
            mediawikiJs: {
                files: [
                    "js/*.js",
                    "js/*.htc"
                ],
                tasks: [
                    "copyto:mediawikiJs"
                ]
            },
            bootstrapJs: {
                files: [
                    "js/bootstrap/*.js"
                ],
                tasks: [
                    "concat:bootstrapMediawikiJs",
                    "uglify:bootstrapMediawikiJs"
                ]
            }
        },

        clean: {
            distMediawiki: ["dist-mediawiki"],
            distBootstrap: ["dist-bootstrap"]
        }
    });

    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-concat");
    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.loadNpmTasks("grunt-recess");
    grunt.loadNpmTasks("grunt-copy-to");

    grunt.registerTask(
        "dist-mediawiki",
        [
            "clean:distMediawiki",
            "concat:bootstrapMediawikiJs",
            "uglify:bootstrapMediawikiJs",
            "recess:bootstrapMediawikiCss",
            "recess:bootstrapMediawikiMinCss",
            "recess:labstrapCss",
            "recess:labstrapMinCss",
            "copyto:mediawikiFonts",
            "copyto:mediawikiJs",
            "copyto:mediawikiPhp",
            "copyto:mediawikiFontAwesome"
        ]
    );

    grunt.registerTask(
        "dist-bootstrap",
        [
            "clean:distBootstrap",
            "concat:bootstrapStandaloneJs",
            "uglify:bootstrapStandaloneJs",
            "recess:bootstrapStandaloneCss",
            "recess:bootstrapStandaloneMinCss",
            "copyto:bootstrapFonts",
            "copyto:bootstrapExample"
        ]
    );

    grunt.registerTask("default", "dist-mediawiki");
};