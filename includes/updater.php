<?php
class HTML5_Game_Plugin_Updater {
    private $plugin_file;
    private $plugin_slug;
    private $repo_url;
    private $author;
    private $version;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
    }

    public function set_plugin_slug($slug) {
        $this->plugin_slug = $slug;
    }

    public function set_repo_url($url) {
        $this->repo_url = $url;
    }

    public function set_author($author) {
        $this->author = $author;
    }

    public function set_version($version) {
        $this->version = $version;
    }

    public function init() {
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('upgrader_post_install', array($this, 'post_update'), 10, 2);
    }

    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $plugin_data = get_plugin_data($this->plugin_file);
        $remote = $this->get_remote_version();

        if (version_compare($plugin_data['Version'], $remote['version'], '<')) {
            $transient->response[$this->plugin_file] = $remote;
        }

        return $transient;
    }

    public function post_update($response, $hook_extra) {
        if ($hook_extra['plugin'] == $this->plugin_file) {
            $this->clear_cache();
        }

        return $response;
    }

    private function get_remote_version() {
        $url = $this->repo_url . '/releases/latest';
        $remote_version_data = wp_remote_get($url);

        if (is_wp_error($remote_version_data)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($remote_version_data), true);
        return array(
            'version' => $data['tag_name'],
            'url' => $data['html_url'],
            'slug' => $this->plugin_slug,
            'author' => $this->author
        );
    }

    private function clear_cache() {
        delete_site_transient('update_plugins');
    }
}
