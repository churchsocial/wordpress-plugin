<?php

namespace ChurchSocial;

class SermonArchive
{
    protected $api_key;
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
        }
    }

    public function loadSermonData($sermon_id)
    {
        if ($this->sermon) {
            return;
        }

        $response = wp_remote_get('https://churchsocialapp.com/api/sermons/'.$sermon_id, [
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

        $this->sermon = json_decode($response['body'], true);
    }

    public function loadSermonsData()
    {
        if ($this->sermons) {
            return;
        }

        $response = wp_remote_get('https://churchsocialapp.com/api/sermons', [
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
        $this->sermons = $response['data'];
        $this->authors = $response['meta']['authors'];

        if (isset($_GET['author']) and $_GET['author']) {
            $this->sermons = array_filter($this->sermons, function ($sermon) {
                return $sermon['author'] === $_GET['author'];
            });
        }
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
            return 'Unable to load sermon archive data.';
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
