<?php

namespace ChurchSocial;

class SermonArchive
{
    const BLOCK = 'church-social/sermon-archive';

    protected $api_key;
    protected $error;
    protected $sermons;
    protected $meta;
    protected $authors;
    protected $sermon;
    protected $sermon_requested = false;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');

        add_action('init', [$this, 'registerBlock']);
        add_action('init', [$this, 'addRewriteRule']);
        add_filter('query_vars', [$this, 'addQueryVars']);
        add_action('template_redirect', [$this, 'loadSermon'], 1);
        add_filter('redirect_canonical', [$this, 'preventCanonicalRedirect']);
        add_filter('document_title_parts', [$this, 'getPageTitle']);
        add_filter('get_canonical_url', [$this, 'getCanonicalUrl'], 10, 2);
    }

    public function registerBlock()
    {
        if (!function_exists('register_block_type_from_metadata')) {
            return;
        }

        register_block_type_from_metadata(dirname(__DIR__).'/blocks/sermon-archive', [
            'render_callback' => [$this, 'renderBlock'],
        ]);

        wp_add_inline_script(
            generate_block_asset_handle(self::BLOCK, 'editorScript'),
            'window.churchSocialSermonArchive = '.wp_json_encode([
                'hasApiKey' => (bool) $this->api_key,
                'settingsUrl' => admin_url('options-general.php?page=church_social'),
            ]).';',
            'before'
        );
    }

    public function addRewriteRule()
    {
        Pages::addRewriteRule(
            self::BLOCK,
            '([0-9]+)(?:/([^/]*))?/?$',
            'church_social_sermon_id=$matches[1]&church_social_sermon_slug=$matches[2]'
        );
    }

    public function addQueryVars($vars)
    {
        $vars[] = 'church_social_sermon_id';
        $vars[] = 'church_social_sermon_slug';

        return $vars;
    }

    /**
     * Load the sermon before the page renders, so the page title is right,
     * missing sermons are a real 404, and old links can be redirected.
     */
    public function loadSermon()
    {
        $page_id = Pages::get(self::BLOCK);

        if (!$page_id || !is_page($page_id)) {
            return;
        }

        $sermon_id = get_query_var('church_social_sermon_id') ?: (isset($_GET['sermon_id']) ? $_GET['sermon_id'] : null);

        if (!$sermon_id) {
            return;
        }

        $this->sermon_requested = true;

        if (!ctype_digit((string) $sermon_id)) {
            Util::send404();

            return;
        }

        $response = $this->request('/sermons/'.$sermon_id);
        $status = wp_remote_retrieve_response_code($response);

        if (in_array($status, [403, 404, 410], true)) {
            Util::send404();

            return;
        }

        if ($status !== 200) {
            $this->error = true;

            return;
        }

        $this->sermon = json_decode(wp_remote_retrieve_body($response), true)['data'];

        // Redirect old ?sermon_id= links, and links with an outdated title.
        if (get_option('permalink_structure')) {
            $is_old_link = isset($_GET['sermon_id']);
            $is_wrong_slug = sanitize_title(get_query_var('church_social_sermon_slug')) !== sanitize_title($this->sermon['title']);

            if ($is_old_link || $is_wrong_slug) {
                wp_safe_redirect(self::sermonUrl($this->sermon), 301);
                exit;
            }
        }
    }

    public function preventCanonicalRedirect($redirect_url)
    {
        return $this->sermon_requested ? false : $redirect_url;
    }

    public function renderBlock($attributes)
    {
        $wrapper_attributes = function_exists('get_block_wrapper_attributes')
            ? get_block_wrapper_attributes()
            : 'class="wp-block-church-social-sermon-archive"';

        return '<div '.$wrapper_attributes.'>'.$this->render().'</div>';
    }

    protected function render()
    {
        if ($this->sermon) {
            ob_start();
            include dirname(__DIR__).'/views/sermon.php';

            return ob_get_clean();
        }

        if (!$this->error) {
            $this->loadSermons();
            $this->loadAuthors();
        }

        if ($this->error) {
            return '<p class="church_social_sermon_archive__error">Unable to load sermon data.</p>';
        }

        ob_start();
        include dirname(__DIR__).'/views/sermons.php';

        return ob_get_clean();
    }

    protected function loadSermons()
    {
        if (is_array($this->sermons)) {
            return;
        }

        $response = $this->request('/sermons?'.http_build_query([
            'page' => isset($_GET['sermon_page']) ? $_GET['sermon_page'] : 1,
            'author_id' => isset($_GET['author_id']) ? $_GET['author_id'] : null,
        ]));

        if (wp_remote_retrieve_response_code($response) !== 200) {
            $this->error = true;

            return;
        }

        $response = json_decode(wp_remote_retrieve_body($response), true);
        $this->sermons = $response['data'];
        $this->meta = $response['meta'];
    }

    protected function loadAuthors()
    {
        if (is_array($this->authors)) {
            return;
        }

        $response = $this->request('/sermon-authors');

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return;
        }

        $this->authors = json_decode(wp_remote_retrieve_body($response), true)['data'];
    }

    protected function request($path)
    {
        if (!$this->api_key) {
            return null;
        }

        return wp_remote_get(CHURCH_SOCIAL_DOMAIN.'/public/church/'.rawurlencode($this->api_key).$path);
    }

    public static function archiveUrl($args = [])
    {
        return Pages::url(self::BLOCK, $args);
    }

    public static function sermonUrl($sermon)
    {
        if (!get_option('permalink_structure')) {
            return self::archiveUrl(['sermon_id' => $sermon['id']]);
        }

        $slug = sanitize_title($sermon['title']);

        return user_trailingslashit(trailingslashit(self::archiveUrl()).$sermon['id'].($slug ? '/'.$slug : ''));
    }

    public function getPageTitle($title)
    {
        if ($this->sermon) {
            $title['title'] = $this->sermon['title'];
        }

        return $title;
    }

    public function getCanonicalUrl($canonical_url, $post)
    {
        if (Pages::get(self::BLOCK) !== $post->ID) {
            return $canonical_url;
        }

        if ($this->sermon) {
            return self::sermonUrl($this->sermon);
        }

        if (isset($_GET['sermon_page'])) {
            return self::archiveUrl(['sermon_page' => (int) $_GET['sermon_page']]);
        }

        return $canonical_url;
    }
}
