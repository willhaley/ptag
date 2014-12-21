module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
            },
            build: {
                src: 'ptag-script.js',
                dest: 'ptag-script.min.js'
            }
        },
        jshint: {
            options: {
                quotmark: 'single',
                curly: true,
                eqeqeq: true,
                eqnull: true,
                browser: true,
                globals: {jQuery: true, tagBox: true}
            },
            files: {
                src: ['ptag-script.js']
            }
        },
        autoprefixer: {

            options: {
                browsers: ['last 2 versions', 'ie 8', 'ie 9']
            },

            no_dest: {
                src: 'ptag-style.css'
            }

        },
        cssmin: {
            add_banner: {
                options: {
                    banner: '/* <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */'
                },
                files: {
                    'ptag-style.min.css': ['ptag-style.css']
                }
            }
        },
        watch: {
            js: {
                files: 'ptag-script.js',
                tasks: ['jshint', 'uglify']
            },
            css: {
                files: 'ptag-style.css',
                tasks: ['autoprefixer', 'cssmin']
            }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task(s).
    grunt.registerTask('default', ['jshint', 'uglify', 'autoprefixer', 'cssmin']);

};