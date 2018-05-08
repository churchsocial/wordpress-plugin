<?php

namespace ChurchSocial;

use WP_Widget;

class RecentSermonsWidget extends WP_Widget
{
    protected $api_key;
    protected $page_id;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');
        $this->page_id = (int) get_option('church_social_sermon_archive_page_id');

        parent::__construct('church_social_recent_sermons', 'Recent Sermons', [
            'description' => 'Show recent sermons from your Church Social sermon archive.',
        ]);

        add_action('widgets_init', function () {
            register_widget('ChurchSocial\RecentSermonsWidget');
        });
    }

    public function form($instance)
    {
        $number_of_sermons = $instance['number_of_sermons'] ? (int) $instance['number_of_sermons'] : 2;

        echo '<p>';
        echo '<label for="'.$this->get_field_id('number_of_sermons').'">How many sermons to show:</label>';
        echo '<select id="'.$this->get_field_id('number_of_sermons').'" name="'.$this->get_field_name('number_of_sermons').'" style="width: 100%;">';

        foreach (range(1, 15) as $number) {
            if ($number_of_sermons === $number) {
                echo '<option selected>'.$number.'</option>';
            } else {
                echo '<option>'.$number.'</option>';
            }
        }

        echo '</select>';
        echo '</p>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['number_of_sermons'] = strip_tags($new_instance['number_of_sermons']);

        return $instance;
    }

    public function widget($args, $instance)
    {
        $sermon_archive_page_url = $this->page_id ? get_permalink($this->page_id) : null;

        $response = wp_remote_get(CHURCH_SOCIAL_DOMAIN.'/api/sermons?limit='.$instance['number_of_sermons'], [
            'headers' => [
                'Authorization' => $this->api_key,
            ],
        ]);

        if (is_array($response) and $response['response']['code'] !== 404) {
            $response = json_decode($response['body'], true);
            $sermons = $response['data'];
        }

        echo $args['before_widget'];
        echo $args['before_title'].apply_filters('widget_title', 'Recent Sermons').$args['after_title'];
        include dirname(__DIR__).'/views/recent_sermons.php';
        echo $args['after_widget'];
    }
}
