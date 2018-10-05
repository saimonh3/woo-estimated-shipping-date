'use strict';
module.exports = function(grunt) {

    grunt.initConfig({
        // setting folder templates
        addtextdomain: {
            options: {
                textdomain: 'wcesd',
            },
            update_all_domains: {
                options: {
                    updateDomains: true
                },
                src: [ '*.php', '**/*.php', '!node_modules/**', '!src/**', '!php-tests/**', '!bin/**', '!build/**', '!assets/**' ]
            }
        },

        // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['build/.*', 'node_modules/*', 'assets/*', 'tests/*', 'bin/*'],
                    mainFile: 'woo-estimated-shipping-date.php',
                    domainPath: '/languages/',
                    potFilename: 'wcesd.pot',
                    type: 'wp-plugin',
                    updateTimestamp: true,
                    potHeaders: {
                        'report-msgid-bugs-to': 'https://saimonsplugins.com/contact/',
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>',
                        poedit: true,
                        'x-poedit-keywordslist': true
                    }
                }
            }
        },
    });

    grunt.loadNpmTasks( 'grunt-wp-i18n' );

    grunt.registerTask('release', 'makepot');
};
