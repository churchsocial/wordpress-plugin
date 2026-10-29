<?php

namespace ChurchSocial;

/**
 * Runs once after the plugin is installed or updated.
 */
class Upgrade
{
    const VERSION = '2.0.0';

    public function __construct()
    {
        add_action('init', [$this, 'run'], 5);
    }

    public function run()
    {
        $version = get_option('church_social_version');

        if ($version === self::VERSION) {
            return;
        }

        update_option('church_social_version', self::VERSION);

        if (!$version) {
            $this->moveSettingsToBlocks();
        }

        Pages::sync();
    }

    /**
     * Before 2.0, the sermon archive and calendar were added to pages chosen
     * on the settings page. Add the blocks to those pages instead, so sites
     * keep working after updating.
     */
    protected function moveSettingsToBlocks()
    {
        // Read every setting first: saving a page updates the page settings.
        $sermon_archive_page_id = get_option('church_social_sermon_archive_page_id');
        $calendar_page_id = get_option('church_social_calendar_page_id');
        $copyright_notice = trim((string) get_option('church_social_sermon_copyright_notice'));

        $this->addBlock($sermon_archive_page_id, SermonArchive::BLOCK, $copyright_notice
            ? "\n\n<!-- wp:paragraph -->\n<p><small>".nl2br(wp_kses_post($copyright_notice), false)."</small></p>\n<!-- /wp:paragraph -->"
            : '');

        $this->addBlock($calendar_page_id, Calendar::BLOCK);

        delete_option('church_social_sermon_copyright_notice');
    }

    protected function addBlock($page_id, $block, $after = '')
    {
        $page = $page_id ? get_post((int) $page_id) : null;

        if (!$page || $page->post_type !== 'page' || has_block($block, $page)) {
            return;
        }

        $content = trim($page->post_content);
        $content .= ($content ? "\n\n" : '').'<!-- wp:'.$block.' /-->'.$after;

        // This can run on any request, so don't let the visitor's
        // permissions filter the page content.
        kses_remove_filters();
        wp_update_post(['ID' => $page->ID, 'post_content' => wp_slash($content)]);
        kses_init();
    }
}
