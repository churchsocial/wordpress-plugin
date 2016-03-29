<?php

namespace ChurchSocial;

class Settings
{
    protected $plugin_file;

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;

        if (is_admin()) {
            $this->registerOptions();
            $this->addPage();
            $this->addPluginLink();
        }
    }

    protected function registerOptions()
    {
        register_activation_hook($this->plugin_file, function () {
            add_option('church_social_api_key', '');
            add_option('church_social_theme', '');
            add_option('church_social_calendar_page_id', '');
            add_option('church_social_sermon_archive_page_id', '');
        });

        register_deactivation_hook($this->plugin_file, function () {
            delete_option('church_social_api_key');
            delete_option('church_social_theme');
            delete_option('church_social_calendar_page_id');
            delete_option('church_social_sermon_archive_page_id');
        });

        add_action('admin_init', function () {
            register_setting('church_social', 'church_social_api_key');
            register_setting('church_social', 'church_social_theme');
            register_setting('church_social', 'church_social_calendar_page_id');
            register_setting('church_social', 'church_social_sermon_archive_page_id');
        });
    }

    protected function addPage()
    {
        add_action('admin_menu', function () {
            add_submenu_page(
                'options-general.php',
                'Church Social',
                'Church Social',
                'manage_options',
                'church_social',
                function () {
                    include dirname($this->plugin_file).'/views/admin.php';
                }
            );
        });
    }

    protected function addPluginLink()
    {
        add_filter('plugin_action_links_'.plugin_basename($this->plugin_file), function ($links) {
            return array_merge($links, [
                '<a href="'.admin_url('options-general.php?page=church_social').'">Settings</a>',
            ]);
        });
    }
}
