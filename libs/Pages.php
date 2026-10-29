<?php

namespace ChurchSocial;

/**
 * Keeps track of which page holds the Sermon Archive block and which holds
 * the Calendar block, so sermons and events can have URLs under those pages
 * and widgets can link to them.
 */
class Pages
{
    const BLOCKS = [
        'church-social/sermon-archive' => 'church_social_sermon_archive_page_id',
        'church-social/calendar' => 'church_social_calendar_page_id',
    ];

    protected static $rewrite_rules = [];

    public function __construct()
    {
        add_action('save_post_page', [self::class, 'sync']);
        add_action('trashed_post', [self::class, 'sync']);
        add_action('untrashed_post', [self::class, 'sync']);
        add_action('deleted_post', [self::class, 'sync']);
        add_action('init', [self::class, 'flushRewriteRulesIfChanged'], 99);
    }

    /**
     * Find the first published page that has each block.
     */
    public static function sync()
    {
        global $wpdb;

        foreach (self::BLOCKS as $block => $option) {
            $page_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND post_content LIKE %s ORDER BY ID",
                '%'.$wpdb->esc_like('<!-- wp:'.$block).'%'
            ));

            $page_id = '';

            foreach ($page_ids as $id) {
                if (has_block($block, (int) $id)) {
                    $page_id = (string) $id;
                    break;
                }
            }

            if (get_option($option) !== $page_id) {
                update_option($option, $page_id);
            }
        }
    }

    public static function get($block)
    {
        return (int) get_option(self::BLOCKS[$block]);
    }

    public static function url($block, $args = [])
    {
        $page_id = self::get($block);
        $url = $page_id ? get_permalink($page_id) : get_permalink();

        return $args ? add_query_arg($args, $url) : $url;
    }

    /**
     * Add a URL pattern under the block's page, like /sermons/123/title.
     */
    public static function addRewriteRule($block, $regex, $query)
    {
        $page_id = self::get($block);
        $page_uri = $page_id && get_option('permalink_structure') ? get_page_uri($page_id) : null;

        self::$rewrite_rules[$block] = $page_uri ? $page_id.':'.$page_uri : '';

        if ($page_uri) {
            add_rewrite_rule(
                '^'.preg_quote($page_uri, '#').'/'.$regex,
                'index.php?page_id='.$page_id.'&'.$query,
                'top'
            );
        }
    }

    /**
     * WordPress saves rewrite rules, so rebuild them when a block moves to
     * another page or its page's URL changes.
     */
    public static function flushRewriteRulesIfChanged()
    {
        $key = md5(serialize(self::$rewrite_rules));

        if (get_option('church_social_rewrite_rules') !== $key) {
            flush_rewrite_rules(false);
            update_option('church_social_rewrite_rules', $key);
        }
    }
}
