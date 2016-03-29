<?php

namespace ChurchSocial;

class Styles
{
    protected $plugin_file;

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
        $this->theme = get_option('church_social_theme');

        if (is_admin()) {
            $this->loadAdminStyles();
        } else {
            $this->loadThemeStyles();
        }
    }

    protected function loadAdminStyles()
    {
        add_filter('admin_enqueue_scripts', function () {
            wp_register_style('church_social', plugins_url('/css/admin.css', $this->plugin_file));
            wp_enqueue_style('church_social');
        });
    }

    protected function loadThemeStyles()
    {
        if ($this->theme) {
            add_filter('wp_enqueue_scripts', function () {
                wp_register_style('church_social', plugins_url('/css/'.$this->theme.'.css', $this->plugin_file));
                wp_enqueue_style('church_social');
            });
        }
    }
}
