module.exports = (grunt) ->
  @loadNpmTasks 'grunt-pot'
  @loadNpmTasks 'grunt-po2mo'
  @loadNpmTasks 'grunt-checktextdomain'

  @initConfig
    pkg: @file.readJSON('package.json')

    po2mo:
      files:
        src: 'lang/*.po',
        expand: true

    pot:
      options:
        text_domain: 'multisite-maintenance-mode',
        dest: 'lang/',
        keywords: [
          '__:1',
          '_e:1',
          '_x:1,2c',
          'esc_html__:1',
          'esc_html_e:1',
          'esc_html_x:1,2c',
          'esc_attr__:1',
          'esc_attr_e:1',
          'esc_attr_x:1,2c',
          '_ex:1,2c',
          '_n:1,2',
          '_nx:1,2,4c',
          '_n_noop:1,2',
          '_nx_noop:1,2,3c'
        ],
      files:
        src: ['multisite-maintenance-mode.php', 'views/admin.php'],
        expand: true

    checktextdomain:
      options:
        text_domain: 'multisite-maintenance-mode',
        correct_domain: true,
        keywords: [
          '__:1',
          '_e:1',
          '_x:1,2c',
          'esc_html__:1',
          'esc_html_e:1',
          'esc_html_x:1,2c',
          'esc_attr__:1',
          'esc_attr_e:1',
          'esc_attr_x:1,2c',
          '_ex:1,2c',
          '_n:1,2',
          '_nx:1,2,4c',
          '_n_noop:1,2',
          '_nx_noop:1,2,3c'
        ],
      files:
        src: ['multisite-maintenance-mode.php', 'views/admin.php'],
        expand: true

