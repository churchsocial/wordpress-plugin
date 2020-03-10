<?php

namespace ChurchSocial;

class SermonArchive
{
    protected $api_key;
    protected $error;
    protected $page_id;
    protected $sermons;
    protected $authors;
    protected $sermon;

    public function __construct()
    {
        $this->api_key = get_option('church_social_api_key');
        $this->page_id = (int) get_option('church_social_sermon_archive_page_id');

        add_filter('document_title_parts', [$this, 'getPageTitle']);
        add_filter('the_content', [$this, 'getContent']);
    }

    public function loadData()
    {
        global $post;

        if ($this->page_id !== $post->ID) {
            return;
        }

        if (isset($_GET['sermon_id'])) {
            $this->loadSermonData($_GET['sermon_id']);
        } else {
            $this->loadSermonsData();
            $this->loadAuthorData();
        }
    }

    public function loadSermonData($sermon_id)
    {
        if ($this->sermon) {
            return;
        }

        $response = wp_remote_get(
            CHURCH_SOCIAL_DOMAIN.'/public/church/'.$this->api_key.'/sermons/'.$sermon_id
        );

        if (!is_array($response) || $response['response']['code'] !== 200) {
            $this->error = true;

            return;
        }

        $this->sermon = json_decode($response['body'], true)['data'];
    }

    public function loadSermonsData()
    {
        if ($this->sermons) {
            return;
        }

        $response = wp_remote_get(
            CHURCH_SOCIAL_DOMAIN.'/public/church/'.$this->api_key.'/sermons?'.http_build_query([
                'page' => get_query_var('page'),
                'author' => (isset($_GET['author_id']) ? $_GET['author_id'] : null),
            ])
        );

        if (!is_array($response) || $response['response']['code'] !== 200) {
            $this->error = true;

            return;
        }

        $response = json_decode($response['body'], true);
        $this->sermons = $response['data'];
        $this->meta = $response['meta'];
    }

    public function loadAuthorData()
    {
        if ($this->authors) {
            return;
        }

        $response = wp_remote_get(
            CHURCH_SOCIAL_DOMAIN.'/public/church/'.$this->api_key.'/sermon-authors'
        );

        if (!is_array($response) || $response['response']['code'] !== 200) {
            return;
        }

        $response = json_decode($response['body'], true);
        $this->authors = $response['data'];
    }

    public function getPageTitle($title)
    {
        $this->loadData();

        if ($this->sermon) {
            $title['title'] = $this->sermon['title'];
        }

        return $title;
    }

    public function getContent($content)
    {
        $this->loadData();

        if ($this->error) {
            return 'Unable to load sermon data.';
        }

        if (is_array($this->sermons)) {
            ob_start();
            include dirname(__DIR__).'/views/sermons.php';

            return ob_get_clean();
        }

        if (is_array($this->sermon)) {
            ob_start();
            include dirname(__DIR__).'/views/sermon.php';

            return ob_get_clean();
        }

        return $content;
    }
}
