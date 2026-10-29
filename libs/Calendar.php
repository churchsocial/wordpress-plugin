<?php

namespace ChurchSocial;

class Calendar
{
    const BLOCK = 'church-social/calendar';

    protected $api_key;
    protected $error;
    protected $events;
    protected $previous_month;
    protected $current_month;
    protected $next_month;
    protected $this_month;
    protected $event;
    protected $event_requested = false;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');

        add_action('init', [$this, 'registerBlock']);
        add_action('init', [$this, 'addRewriteRule']);
        add_filter('query_vars', [$this, 'addQueryVars']);
        add_action('template_redirect', [$this, 'loadEvent'], 1);
        add_filter('redirect_canonical', [$this, 'preventCanonicalRedirect']);
        add_filter('document_title_parts', [$this, 'getPageTitle']);
        add_filter('get_canonical_url', [$this, 'getCanonicalUrl'], 10, 2);
    }

    public function registerBlock()
    {
        if (!function_exists('register_block_type_from_metadata')) {
            return;
        }

        register_block_type_from_metadata(dirname(__DIR__).'/blocks/calendar', [
            'render_callback' => [$this, 'renderBlock'],
        ]);

        wp_add_inline_script(
            generate_block_asset_handle(self::BLOCK, 'editorScript'),
            'window.churchSocialCalendar = '.wp_json_encode([
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
            '([0-9]+)/([0-9]{4}-[0-9]{2}-[0-9]{2})(?:/([^/]*))?/?$',
            'church_social_event_id=$matches[1]&church_social_event_date=$matches[2]&church_social_event_slug=$matches[3]'
        );
    }

    public function addQueryVars($vars)
    {
        $vars[] = 'church_social_event_id';
        $vars[] = 'church_social_event_date';
        $vars[] = 'church_social_event_slug';

        return $vars;
    }

    /**
     * Load the event before the page renders, so the page title is right,
     * missing events are a real 404, and old links can be redirected.
     */
    public function loadEvent()
    {
        $page_id = Pages::get(self::BLOCK);

        if (!$page_id || !is_page($page_id)) {
            return;
        }

        $event_id = get_query_var('church_social_event_id') ?: (isset($_GET['event_id']) ? $_GET['event_id'] : null);
        $event_date = get_query_var('church_social_event_date') ?: (isset($_GET['event_date']) ? $_GET['event_date'] : null);

        if (!$event_id) {
            return;
        }

        $this->event_requested = true;

        if (!ctype_digit((string) $event_id) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $event_date)) {
            Util::send404();

            return;
        }

        $response = $this->request('/events/'.$event_id.'/'.$event_date);
        $status = wp_remote_retrieve_response_code($response);

        if (in_array($status, [403, 404, 410], true)) {
            Util::send404();

            return;
        }

        if ($status !== 200) {
            $this->error = true;

            return;
        }

        $event = json_decode(wp_remote_retrieve_body($response), true)['data'];
        $event['date'] = Util::date($event['date']);
        $event['start_time'] = $event['start_time'] ? $event['date']->modify($event['start_time']) : null;
        $event['end_time'] = $event['end_time'] ? $event['date']->modify($event['end_time']) : null;

        $this->event = $event;

        // Redirect old ?event_id= links, and links with an outdated title.
        if (get_option('permalink_structure')) {
            $is_old_link = isset($_GET['event_id']);
            $is_wrong_slug = sanitize_title(get_query_var('church_social_event_slug')) !== sanitize_title($this->event['title']);

            if ($is_old_link || $is_wrong_slug) {
                wp_safe_redirect(self::eventUrl($this->event), 301);
                exit;
            }
        }
    }

    public function preventCanonicalRedirect($redirect_url)
    {
        return $this->event_requested ? false : $redirect_url;
    }

    public function renderBlock($attributes)
    {
        $wrapper_attributes = function_exists('get_block_wrapper_attributes')
            ? get_block_wrapper_attributes()
            : 'class="wp-block-church-social-calendar"';

        return '<div '.$wrapper_attributes.'>'.$this->render().'</div>';
    }

    protected function render()
    {
        if ($this->event) {
            ob_start();
            include dirname(__DIR__).'/views/event.php';

            return ob_get_clean();
        }

        if (!$this->error) {
            $this->loadEvents();
        }

        if ($this->error) {
            return '<p class="church_social_calendar__error">Unable to load calendar data.</p>';
        }

        ob_start();
        include dirname(__DIR__).'/views/calendar.php';

        return ob_get_clean();
    }

    protected function loadEvents()
    {
        if (is_array($this->events)) {
            return;
        }

        $month = self::requestedMonth();
        $response = $this->request('/events'.($month ? '?'.http_build_query(['month' => $month]) : ''));

        if (wp_remote_retrieve_response_code($response) !== 200) {
            $this->error = true;

            return;
        }

        $response = json_decode(wp_remote_retrieve_body($response), true);
        $this->events = $response['data'];
        $this->previous_month = $response['meta']['previous_month'];
        $this->current_month = Util::date($response['meta']['current_month']);
        $this->next_month = $response['meta']['next_month'];
        $this->this_month = Util::date('today', 'Y-m');
    }

    protected function request($path)
    {
        if (!$this->api_key) {
            return null;
        }

        return wp_remote_get(CHURCH_SOCIAL_DOMAIN.'/public/church/'.rawurlencode($this->api_key).$path);
    }

    protected static function requestedMonth()
    {
        if (isset($_GET['month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month'])) {
            return $_GET['month'];
        }

        return null;
    }

    public static function calendarUrl($args = [])
    {
        return Pages::url(self::BLOCK, $args);
    }

    public static function eventUrl($event)
    {
        $date = $event['date'] instanceof \DateTimeInterface
            ? $event['date']->format('Y-m-d')
            : Util::date($event['date'], 'Y-m-d');

        if (!get_option('permalink_structure')) {
            return self::calendarUrl(['event_id' => $event['id'], 'event_date' => $date]);
        }

        $slug = sanitize_title($event['title']);

        return user_trailingslashit(trailingslashit(self::calendarUrl()).$event['id'].'/'.$date.($slug ? '/'.$slug : ''));
    }

    public function getPageTitle($title)
    {
        if ($this->event) {
            $title['title'] = $this->event['title'];
        }

        return $title;
    }

    public function getCanonicalUrl($canonical_url, $post)
    {
        if (Pages::get(self::BLOCK) !== $post->ID) {
            return $canonical_url;
        }

        if ($this->event) {
            return self::eventUrl($this->event);
        }

        if (self::requestedMonth()) {
            return self::calendarUrl(['month' => self::requestedMonth()]);
        }

        return $canonical_url;
    }
}
