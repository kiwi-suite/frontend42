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
            all: ['compile-vendor-js', 'compile-app-js', 'less:app', 'copy']
        },

        concat: {
            options: {
                separator: ';'
            },
            vendor: {
                src: [
                    '<%= vendor_dir %>/jquery/dist/jquery.js',
                    '<%= vendor_dir %>/angular/angular.js',
                    '<%= vendor_dir %>/angular-bootstrap/ui-bootstrap.js',
                    '<%= vendor_dir %>/angular-bootstrap/ui-bootstrap-tpls.js',
                    '<%= vendor_dir %>/angular-animate/angular-animate.js',
                    '<%= vendor_dir %>/angular-smart-table/dist/smart-table.min.js',
                    '<%= vendor_dir %>/angular-ui-utils/ui-utils.js',
                    '<%= vendor_dir %>/screenfull/dist/screenfull.js',
                    '<%= vendor_dir %>/moment/min/moment-with-locales.js',
                    '<%= vendor_dir %>/moment-timezone/builds/moment-timezone-with-data.js'
                ],
                dest: '<%= dist %>/js/vendor.js'
            },
            app: {
                src: [
                    'javascripts/*.js',
                    'javascripts/directive/*.js',
                    'javascripts/filter/*.js',
                    'javascripts/controller/*.js'
                ],
                dest: '<%= dist %>/js/admin42.js'
            }
        },

        uglify: {
            options: {
                mangle: false
            },
            vendor: {
                src: '<%= dist %>/js/vendor.js',
                dest: '<%= dist %>/js/vendor.min.js'
            },
            app: {
                src: '<%= dist %>/js/admin42.js',
                dest: '<%= dist %>/js/admin42.min.js'
            }
        },

        less: {
            options: {
                compress: true,
                cleancss: true
            },
            app: {
                files: {
                    '<%= dist %>/css/admin42.min.css': [
                        '<%= vendor_dir %>/animate.css/animate.css',
                        'less/main.less'
                    ]
                }
            }
        },

        copy: {
            bootstrap: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['<%= vendor_dir %>/bootstrap/fonts/*'],
                        dest: '<%= dist %>/fonts/',
                        filter: 'isFile'
                    }
                ]
            },
            fontawesome: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['<%= vendor_dir %>/font-awesome/fonts/*'],
                        dest: '<%= dist %>/fonts/',
                        filter: 'isFile'
                    }
                ]
            },
            simpleline: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['<%= vendor_dir %>/simple-line-icons/fonts/*'],
                        dest: '<%= dist %>/fonts/',
                        filter: 'isFile'
                    }
                ]
            },
            images: {
                files: [
                    {
                        expand: true,
                        cwd: 'images/',
                        src: '**',
                        dest: '<%= dist %>/images/'
                    }
                ]
            }
        },

        clean: {
            all: ['<%= dist %>/fonts/', '<%= dist %>/css/', '<%= dist %>/js/', '<%= dist %>/images/'],

            vendorjs: ['<%= dist %>/js/vendor.js'],
            appjs: ['<%= dist %>/js/admin42.js']
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
    grunt.registerTask('compile-vendor-js', ['concat:vendor', 'uglify:vendor', 'clean:vendorjs']);
    grunt.registerTask('compile-app-js', ['concat:app', 'uglify:app', 'clean:appjs']);
    grunt.registerTask('compile-css', ['less:app']);
    grunt.registerTask('clear', ['clean:all']);



    require('load-grunt-tasks')(grunt);
};
