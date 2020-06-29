"use strict";

module.exports = function (grunt) {

    // Module Requires
    // --------------------------
    require("load-grunt-tasks")(grunt);
    require("time-grunt")(grunt);
    require("grunt-contrib-concat")(grunt);

    // Init Config
    // --------------------------

    var appConfig = {

        concat: {
            '<%= dirs.css %>/libs.css': [
                "<%= dirs.node_modules %>/owl.carousel/dist/assets/owl.carousel.min.css",
                "<%= dirs.node_modules %>/owl.carousel/dist/assets/owl.theme.default.min.css",
                "<%= dirs.node_modules %>/material-design-lite/dist/material.min.css",
                "<%= dirs.node_modules %>/bootstrap/dist/css/bootstrap.min.css",

            ],
            "<%= dirs.jsfinal %>/libs.min.js": [
                "<%= dirs.node_modules %>/owl.carousel/dist/owl.carousel.min.js",
                "<%= dirs.node_modules %>/material-design-lite/dist/material.min.js",
                "<%= dirs.node_modules %>/jquery/dist/jquery.min.js",
                "<%= dirs.node_modules %>/bootstrap/dist/js/bootstrap.min.js",
                "<%= dirs.node_modules %>/sweetalert/dist/sweetalert.min.js",
                "<%= dirs.libs%>/*.js"


            ]
        },

        // Dirs
        dirs: {
            // PATHS SRC
            js: "assets/js",
            sass: "assets/sass",
            img: "assets/images",
            libs: "assets/lib",
            node_modules: "node_modules/",

            // FRAMEWORK PATHS
            base: '../FRAMEWORK',
            controller: '../FRAMEWORK/app/Controllers',
            model: '../FRAMEWORK/app/Models',
            views: '../FRAMEWORK/app/Views',
            lib: '..//FRAMEWORK/app/Lib',
            // ASSETS FRAMEWORK
            css: "../FRAMEWORK/app/webroot/css",
            jsfinal: "../FRAMEWORK/app/webroot/js",
            imgdest: "../FRAMEWORK/app/webroot/img"
        },

        except: [
            "Thumbs.db",
            ".git",
            ".gitignore",
            "sftp-config.json",
            // "app/webroot/js/tinymce",
            "app/Controllers/_initController.php",
            "app/Views/_init",
            "system/manage.py",
        ],

        // Metadata
        pkg: grunt.file.readJSON("package.json"),
        banner:
            "\n" +
            "/*\n" +
            " * -------------------------------------------------------\n" +
            " * Project: <%= pkg.title %>\n" +
            " * Version: <%= pkg.version %>\n" +
            " * Author:  <%= pkg.author.name %> (<%= pkg.author.email %>)\n" +
            " *\n" +
            " * Copyright (c) <%= grunt.template.today(\"yyyy\") %> <%= pkg.title %>\n" +
            " * -------------------------------------------------------\n" +
            " */\n" +
            "\n",


        // Watch Task
        watch: {
            options: {
                livereload: true,
                spawn: false,
                interval: 1000
            },
            css: {
                files: "<%= dirs.sass %>/**",
                tasks: ["compass", "autoprefixer"],
                options: {
                    spawn: false
                }
            },
            js: {
                files: "<%= jshint.all %>",
                tasks: ["jshint", "uglify"]
            },

            // files: [
            //     '<%= dirs.lib %>/{*,}*.php',
            //     '<%= dirs.controller %>/{*,}*.php',
            //     '<%= dirs.model %>/{*,}*.php',
            //     '<%= dirs.views %>/**/{*,}*.phtml'
            // ]
        },

        // Linting
        jshint: {
            options: {
                jshintrc: ".jshintrc"
            },
            all: [
                "Gruntfile.js",
                "<%= dirs.js %>/main.js",
                "<%= dirs.js %>/admin.js"
            ]
        },

        // Minifica e concatena
        uglify: {
            options: {
                mangle: false,
                banner: "<%= banner %>"
            },
            dist: {
                files: {
                    // Seu script do projeto
                    "<%= dirs.jsfinal %>/site.min.js": [
                        "<%= dirs.js %>/main.js"
                    ]
                }
            }
        },

        // Compile Sass/Scss to CSS
        compass: {
            dist: {
                options: {
                    force: true,
                    config: "config.rb",
                }
            }
        },


        // Prefixo automático para css cross browser
        autoprefixer: {
            options: {
                browsers: ['last 5 version']
            },

            single_file: {
                src: '<%= dirs.css %>style.css',
                dest: '<%= dirs.css %>style.css'
            }

        },


        // Otimiza as imagens utilizando o Imagemagick
        shell: {
            imagemagick: {
                command: 'mogrify -strip -interlace Plane -quality 70 <%= dirs.imgdest %>/*.{jpg,png,gif}'
            }
        },

        // FTP Deploy
        ftpush: {
            build: {
                auth: {
                    host: "host",
                    port: 21,
                    authKey: "key1"
                },
                src: "../FRAMEWORK/",
                dest: "/public_html/paginas/testdeploy",
                simple: false,
                exclusions: "<%= except %>"
            }
        },

        // Rsync Deploy
        rsync: {
            options: {
                args: ["--verbose"],
                exclude: "<%= except %>",
                recursive: true,
                syncDest: true
            },
            production: {
                options: {
                    src: "../FRAMEWORK/",
                    dest: "/path/server",
                    host: "user@host.com",
                }
            }
        }
    };

    grunt.initConfig(appConfig);


    // Register tasks
    // --------------------------

    // Start server and watch for changes
    grunt.registerTask("default", ["watch"]);

    // Run build
    grunt.registerTask("build", ["jshint", "uglify", "compass"]);

    // Run only js
    grunt.registerTask("b-js", ["jshint", "uglify"]);

    // Run only sass
    grunt.registerTask("b-css", ["compass"]);

    // Optimize Images
    grunt.registerTask("optimize", ["shell:imagemagick"]);

    // Deploy Methods
    grunt.registerTask("ftp", ["build", "ftpush"]);
    grunt.registerTask("rsync-b", ["build", "rsync"]);

    // Aliases Tasks
    grunt.registerTask("b", ["build"]);
    grunt.registerTask("o", ["optimize"]);
    grunt.registerTask("f", ["ftp"]);
    grunt.registerTask("r", ["rsync-b"]);

};
