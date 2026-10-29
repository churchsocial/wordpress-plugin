<?php

namespace ChurchSocial;

class Livestream
{
    protected $api_key;

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
            generate_block_asset_handle('church-social/livestream', 'editorScript'),
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

        return '<div '.$wrapper_attributes.'>'.$this->renderPlayer().'</div>';
    }

    public function renderShortcode()
    {
        if (!$this->api_key) {
            return '';
        }

        return '<div class="church_social_livestream">'.$this->renderPlayer().'</div>';
    }

    public function getPlayerUrl()
    {
        if (!$this->api_key) {
            return null;
        }

        return CHURCH_SOCIAL_DOMAIN.'/public/church/'.rawurlencode($this->api_key).'/livestream/embed';
    }

    protected function renderPlayer()
    {
        return '<iframe'
            .' src="'.esc_url($this->getPlayerUrl()).'"'
            .' title="Livestream"'
            .' allow="accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share"'
            .' referrerpolicy="strict-origin-when-cross-origin"'
            .' allowfullscreen'
            .' style="display: block; aspect-ratio: 16 / 9; width: 100%; height: auto; border: 0;"'
            .'></iframe>';
    }
}
