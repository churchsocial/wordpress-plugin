<?php

namespace ChurchSocial;

class Calendar
{
    protected $api_key;
    protected $page_id;
    protected $events;
    protected $previous_month;
    protected $current_month;
    protected $next_month;
    protected $event;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');
        $this->page_id = (int) get_option('church_social_calendar_page_id');

        add_filter('document_title_parts', [$this, 'getPageTitle']);
        add_filter('the_content', [$this, 'getContent']);
    }

    public function loadData()
    {
        global $post;

        if ($this->page_id !== $post->ID) {
            return;
        }

        if (isset($_GET['event_id']) and isset($_GET['event_date'])) {
            $this->loadEventData($_GET['event_id'], $_GET['event_date']);
        } else {
            $this->loadEventsData();
        }
    }

    public function loadEventData($event_id, $event_date)
    {
        if ($this->event) {
            return;
        }

        $response = wp_remote_get(CHURCH_SOCIAL_DOMAIN.'/api/events/'.$event_id.'/'.$event_date, [
            'headers' => [
                'Authorization' => $this->api_key,
            ],
        ]);

        if (!is_array($response)) {
            $this->error = true;

            return;
        }

        if ($response['response']['code'] === 404) {
            $this->error = true;

            return;
        }

        $response = json_decode($response['body'], true);
        $event = $response['data'];
        $event['date'] = date_create($event['date']);

        $this->event = $event;
    }

    public function loadEventsData()
    {
        if ($this->events) {
            return;
        }

        $response = wp_remote_get(CHURCH_SOCIAL_DOMAIN.'/api/events'.(isset($_GET['month']) ? '?month='.$_GET['month'] : ''), [
            'headers' => [
                'Authorization' => $this->api_key,
            ],
        ]);

        if (!is_array($response)) {
            $this->error = true;

            return;
        }

        if ($response['response']['code'] === 404) {
            $this->error = true;

            return;
        }

        $response = json_decode($response['body'], true);
        $this->events = $response['data'];
        $this->previous_month = date_create($response['meta']['previous']);
        $this->current_month = date_create($response['meta']['month']);
        $this->next_month = date_create($response['meta']['next']);
    }

    public function getPageTitle($title)
    {
        $this->loadData();

        if ($this->event) {
            $title['title'] = $this->event['title'];
        }

        return $title;
    }

    public function getContent($content)
    {
        $this->loadData();

        if ($this->error) {
            return 'Unable to load calendar data.';
        }

        if (is_array($this->events)) {
            ob_start();
            include dirname(__DIR__).'/views/calendar.php';

            return ob_get_clean();
        }

        if (is_array($this->event)) {
            ob_start();
            include dirname(__DIR__).'/views/event.php';

            return ob_get_clean();
        }

        return $content;
    }
}
