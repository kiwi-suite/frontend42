module.exports = function(grunt) {
    grunt.initConfig({
        vendor_dir: 'bower_components',
        dist: 'dist',

        bower: {
            install: {
                options: {
                    copy: false
                }
            }
        },

        concurrent: {
            all: ['compile-app-js', 'less:app']
        },

        concat: {
            options: {
                separator: ';'
            },
            app: {
                src: [
                    'javascripts/*.js',
                    'javascripts/directive/*.js',
                    'javascripts/filter/*.js',
                    'javascripts/controller/*.js'
                ],
                dest: '<%= dist %>/js/frontend42.js'
            }
        },

        uglify: {
            options: {
                mangle: false
            },
            app: {
                src: '<%= dist %>/js/frontend42.js',
                dest: '<%= dist %>/js/frontend42.min.js'
            }
        },

        less: {
            options: {
                compress: true,
                cleancss: true
            },
            app: {
                files: {
                    '<%= dist %>/css/frontend42.min.css': [
                        'less/main.less'
                    ]
                }
            }
        },

        clean: {
            all: ['<%= dist %>/fonts/', '<%= dist %>/css/', '<%= dist %>/js/', '<%= dist %>/images/'],

            appjs: ['<%= dist %>/js/frontend42.js']
        },

        watch: {
            grunt: {
                files: ['Gruntfile.js', 'bower.json'],
                tasks: ['default']

            },
            js: {
                files: ['javascripts/**/*.js'],
                tasks: ['compile-app-js']
            },
            less: {
                files: ['less/*.less', 'less/**/*.less'],
                tasks: ['compile-css']
            }
        }
    });

    grunt.registerTask('default', ['bower', 'concurrent:all']);
    grunt.registerTask('compile-app-js', ['concat:app', 'uglify:app', 'clean:appjs']);
    grunt.registerTask('compile-css', ['less:app']);
    grunt.registerTask('clear', ['clean:all']);



    require('load-grunt-tasks')(grunt);
};
