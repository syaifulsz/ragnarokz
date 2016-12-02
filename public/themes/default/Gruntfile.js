module.exports = function(grunt) {

    grunt.initConfig({
        // jshint: {
        //     files: ['Gruntfile.js', 'src/**/*.js', 'test/**/*.js'],
        //     options: {
        //         globals: {
        //             jQuery: true
        //         }
        //     }
        // },
        less: {
            main: {
                files: {
                    'style.css': 'style.less'
                }
            }
        },
        // uglify: {
        //     main: {
        //         files: {
        //             'assets/js/global.min.js': [
        //                 'assets/build/javascript/jquery-1.10.2.min.js',
        //                 'assets/build/bootstrap/js/bootstrap.min.js',
        //                 'assets/build/javascript/head.js',
        //                 'assets/build/javascript/scripts.js',
        //                 'assets/build/javascript/mobile.js',
        //                 'assets/build/javascript/global.js'
        //             ]
        //         }
        //     }
        // }
        watch: {
            options: {
                dateFormat: function(time) {
                    grunt.log.writeln('The watch finished in ' + time + 'ms at' + (new Date()).toString());
                    grunt.log.writeln('Waiting for more changes...');
                },
            },
            css: {
                files: ['**/*.less'],
                tasks: ['less'],
                options: {
                    interrupt: true,
                    livereload: true
                }
            }
        }
    });

    grunt.event.on('watch', function(action, filepath, target) {
        grunt.log.writeln(target + ': ' + filepath + ' has ' + action);
    });

    // grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    // grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['less']);
};
