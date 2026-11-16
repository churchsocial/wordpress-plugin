<?php

namespace ChurchSocial;

class Livestream
{
    const BLOCK = 'church-social/livestream';

    protected $api_key;
    protected $livestream;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');

        add_action('init', [$this, 'registerBlock']);
        add_shortcode('church_social_livestream', [$this, 'renderShortcode']);
    }

    public function registerBlock()
    {
        if (!function_exists('register_block_type_from_metadata')) {
            return;
        }

        register_block_type_from_metadata(dirname(__DIR__).'/blocks/livestream', [
            'render_callback' => [$this, 'renderBlock'],
        ]);

        wp_add_inline_script(
            generate_block_asset_handle(self::BLOCK, 'editorScript'),
            'window.churchSocialLivestream = '.wp_json_encode([
                'playerUrl' => $this->getPlayerUrl(),
                'settingsUrl' => admin_url('options-general.php?page=church_social'),
            ]).';',
            'before'
        );
    }

    public function renderBlock($attributes)
    {
        if (!$this->api_key) {
            return '';
        }

        $wrapper_attributes = function_exists('get_block_wrapper_attributes')
            ? get_block_wrapper_attributes()
            : 'class="wp-block-church-social-livestream"';

        return '<div '.$wrapper_attributes.'>'.$this->render().'</div>';
    }

    public function renderShortcode()
    {
        if (!$this->api_key) {
            return '';
        }

        // The block loads these itself, from its block.json.
        wp_enqueue_style(generate_block_asset_handle(self::BLOCK, 'style'));
        wp_enqueue_script(generate_block_asset_handle(self::BLOCK, 'viewScript'));

        return $this->render();
    }

    public function getPlayerUrl()
    {
        if (!$this->api_key) {
            return null;
        }

        return CHURCH_SOCIAL_DOMAIN.'/public/church/'.rawurlencode($this->api_key).'/livestream/embed';
    }

    public function getApiUrl()
    {
        if (!$this->api_key) {
            return null;
        }

        return CHURCH_SOCIAL_DOMAIN.'/public/church/'.rawurlencode($this->api_key).'/livestream';
    }

    protected function render()
    {
        $livestream = $this->loadLivestream();

        ob_start();
        include dirname(__DIR__).'/views/livestream.php';

        return ob_get_clean();
    }

    // Null when the API can't be reached, so the page still shows the player on its own.
    protected function loadLivestream()
    {
        if ($this->livestream !== null) {
            return $this->livestream ?: null;
        }

        $cache_key = 'church_social_livestream_'.md5($this->getApiUrl());
        $livestream = get_transient($cache_key);

        if (is_array($livestream)) {
            $this->livestream = $livestream;

            return $this->livestream;
        }

        // Pages with more than one player only wait on the API once.
        $this->livestream = false;

        $response = wp_remote_get($this->getApiUrl(), ['timeout' => 5]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $data = isset($data['data']) ? $data['data'] : null;

        if (!isset($data['player_url'])) {
            return null;
        }

        $isVideo = function ($video) {
            return isset($video['id'], $video['scheduled_at'], $video['player_url']) && preg_match('/^https?:\/\//i', $video['player_url']);
        };

        // Services with only their own player have no list.
        $this->livestream = [
            'player_url' => $data['player_url'],
            'videos' => isset($data['videos']) && is_array($data['videos']) ? array_values(array_filter($data['videos'], $isVideo)) : null,
        ];

        set_transient($cache_key, $this->livestream, 60);

        return $this->livestream;
    }

    protected function renderPlayer($url)
    {
        return '<iframe'
            .($url ? ' src="'.esc_url($url).'"' : '')
            .' title="Livestream"'
            .' allow="accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share"'
            .' referrerpolicy="strict-origin-when-cross-origin"'
            .' allowfullscreen'
            .' style="display: block; aspect-ratio: 16 / 9; width: 100%; height: auto; border: 0;"'
            .'></iframe>';
    }
}
