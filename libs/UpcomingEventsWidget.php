<?php

namespace ChurchSocial;

use WP_Widget;

class UpcomingEventsWidget extends WP_Widget
{
    protected $api_key;
    protected $page_id;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');
        $this->page_id = (int) get_option('church_social_calendar_page_id');

        parent::__construct('church_social_upcoming_events', 'Upcoming Events', [
            'description' => 'Show upcoming events from your Church Social calendar.',
        ]);

        add_action('widgets_init', function () {
            register_widget('ChurchSocial\UpcomingEventsWidget');
        });
    }

    public function form($instance)
    {
        $number_of_events = $instance['number_of_events'] ? (int) $instance['number_of_events'] : 5;

        echo '<p>';
        echo '<label for="'.$this->get_field_id('number_of_events').'">How many events to show:</label>';
        echo '<select id="'.$this->get_field_id('number_of_events').'" name="'.$this->get_field_name('number_of_events').'" style="width: 100%;">';

        foreach (range(1, 15) as $number) {
            if ($number_of_events === $number) {
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

        $instance['number_of_events'] = strip_tags($new_instance['number_of_events']);

        return $instance;
    }

    public function widget($args, $instance)
    {
        $calendar_page_url = $this->page_id ? get_permalink($this->page_id) : null;

        $response = wp_remote_get('https://app.churchsocial.com/api/events/upcoming?limit='.$instance['number_of_events'], [
            'headers' => [
                'Authorization' => $this->api_key,
            ],
        ]);

        if (is_array($response) and $response['response']['code'] !== 404) {
            $response = json_decode($response['body'], true);
            $events = $response['data'];
        }

        echo $args['before_widget'];
        echo $args['before_title'].apply_filters('widget_title', 'Upcoming Events').$args['after_title'];
        include dirname(__DIR__).'/views/upcoming_events.php';
        echo $args['after_widget'];
    }
}
