<?php

namespace ChurchSocial;

class Styles
{
    public function __construct($plugin_file)
    {
        $theme = get_option('church_social_theme');

        if (!is_admin() && $theme) {
            add_filter('wp_enqueue_scripts', function () use ($theme, $plugin_file) {
                wp_register_style('church_social', plugins_url('/css/'.$theme.'.css', $plugin_file));
                wp_enqueue_style('church_social');
            });
        }
    }
}
