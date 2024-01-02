<?php

define('LIKEBTN_LAST_SUCCESSFULL_SYNC_TIME_OFFSET', 57600);
define('LIKEBTN_API_URL', 'http://api.likebtn.com/api/');
define('LIKEBTN_LOCALES_SYNC_INTERVAL', 57600);
define('LIKEBTN_STYLES_SYNC_INTERVAL', 57600);

class LikeBtn {

    // Delay in case of API error
    const API_ERROR_DELAY = 14400; // 4h

    protected $plugin_params = null;

    protected static $synchronized = false;
    // Cached API request URL.
    protected static $apiurl = '';

    /**
     * Constructor.
     */
    public function __construct($plugin_params) {
        $this->plugin_params = $plugin_params;
    }

    /**
     * Running votes synchronization.
     */
    public function runSyncVotes() {
        if (!self::$synchronized && $this->plugin_params->get('email') && $this->plugin_params->get('api_key') && $this->plugin_params->get('sync_interval') && $this->timeToSyncVotes($this->plugin_params->get('sync_interval') * 60)) {
            $this->syncVotes($this->plugin_params->get('email'), $this->plugin_params->get('api_key'));
        }
    }

    /**
     * Check if it is time to sync votes.
     */
    public function timeToSyncVotes($sync_period) {

        $last_sync_time = $this->plugin_params->get('last_sync_time');

        //plgContentLikebtn::setPluginParameter('last_sync_time', time());
        //return true;

        $now = time();
        if (!$last_sync_time) {
            plgContentLikebtn::setPluginParameter('last_sync_time', $now);
            self::$synchronized = true;
            return true;
        } else {
            if ($last_sync_time + $sync_period > $now) {
                return false;
            } else {
                plgContentLikebtn::setPluginParameter('last_sync_time', $now);
                self::$synchronized = true;
                return true;
            }
        }
    }

    /**
     * Retrieve data.
     */
    public function curl($url) {
        $cms_version = JVERSION;
        //$likebtn_version = _likebtn_like_button_get_plugin_version;

        $likebtn_version = LIKEBTN_VERSION;
        $php_version = phpversion();
        $useragent = "Joomla {$cms_version}; plugin $likebtn_version; PHP $php_version";

        try {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_TIMEOUT, 60);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $response = curl_exec($ch);
          curl_close($ch);
        } catch(Exception $e) { }

        return $response;
    }

    /**
     * Sync votes from LikeBtn.com to local DB.
     */
    public function syncVotes() {
        $sync_result = true;

        $last_sync_time = number_format($this->plugin_params->get('last_sync_time'), 0, '', '');

        $updated_after = '';

        if ($this->plugin_params->get('last_successfull_sync_time')) {
            $updated_after = $this->plugin_params->get('last_successfull_sync_time') - LIKEBTN_LAST_SUCCESSFULL_SYNC_TIME_OFFSET;
        }

        $url = "output=json&last_sync_time=" . $last_sync_time;
        if ($updated_after) {
            $url .= '&updated_after=' . $updated_after;
        }

        // retrieve first page
        $response = $this->apiRequest('stat', $url);
        $this->updateVotes($response);
        //if (!$this->updateVotes($response)) {
        if (isset($response['response']['items'])) {
            $sync_result = true;
        } else {
            // Wrong plan or API error
            // Delay
            $sync_result = false;
            $last_sync_time = time() + self::API_ERROR_DELAY;
            plgContentLikebtn::setPluginParameter('last_sync_time', $last_sync_time);
        }

        // retrieve all pages after the first
        if (isset($response['response']['total']) && isset($response['response']['page_size'])) {
            $total_pages = ceil((int) $response['response']['total'] / (int) $response['response']['page_size']);

            for ($page = 2; $page <= $total_pages; $page++) {
                $response = $this->apiRequest('stat', $url . '&page=' . $page);
                $this->updateVotes($response);
                // if (!$this->updateVotes($response)) {
                //     $sync_result = false;
                // }
                if (!isset($response['response']['items'])) {
                    $sync_result = false;
                }
            }
        }

        if (!$last_sync_time) {
            $last_sync_time = time();
        }
        if ($sync_result) {
            plgContentLikebtn::setPluginParameter('last_successfull_sync_time', $last_sync_time);
        }
    }

    /**
     * Test synchronization.
     *
     * @param type $account_api_key
     * @param type $site_api_key
     */
    /*public function testSync($email, $api_key) {
        $email = trim($email);
        $api_key = trim($api_key);

        $response = $this->apiRequest('stat', 'output=json&page_size=1', $email, $api_key);

        return $response;
    }*/

    /**
     * Decode JSON.
     */
    public function jsonDecode($jsong_string) {
        return json_decode($jsong_string, true);
    }

    /**
     * Update votes in database from API response.
     */
    public function updateVotes($response) {
        $entity_updated = false;

        if (!empty($response['response']['items'])) {
            foreach ($response['response']['items'] as $item) {
                $likes = 0;
                if (!empty($item['likes'])) {
                    $likes = $item['likes'];
                }
                $dislikes = 0;
                if (!empty($item['dislikes'])) {
                    $dislikes = $item['dislikes'];
                }
                $entity_updated = $this->saveItem($item['identifier'], $likes, $dislikes, $item['url']);
            }
        } else {
            $entity_updated = true;
        }
        return $entity_updated;
    }

    /**
     * Update entity custom fields
     */
    public function saveItem($identifier, $likes, $dislikes, $url = '') {
        $result = null;

        $content_types = plgContentLikebtn::getSupportedContentTypes();
        $content_type_valid = false;

        $identifier_parts = explode('_', $identifier);
        preg_match("/^(.*)_(\d+)$/", $identifier, $identifier_parts);

        $content_type = '';
        if (!empty($identifier_parts[1])) {
            $content_type = $identifier_parts[1];
        }

        $content_id = '';
        if (isset($identifier_parts[2])) {
            $content_id = $identifier_parts[2];
        }

        $likes_minus_dislikes = null;
        if ($likes !== null && $dislikes !== null) {
            $likes_minus_dislikes = $likes - $dislikes;
        }

        foreach ($content_types as $content_type_obj) {
            if ($content_type_obj->type_alias == $content_type) {
                $content_type_valid = true;
            }
        }

        if ($content_type_valid && is_numeric($content_id)) {
            // valid content type
        } else {
            $content_type = '';
            $content_id = '';
        }

        $db = JFactory::getDBO();

        $item = new JObject();

        $item->content_type = $content_type;
        $item->content_id = $content_id;
        $item->identifier = $identifier;
        $item->identifier_hash = md5($identifier);
        $item->url = $url;
        $item->likes = $likes;
        $item->dislikes = $dislikes;
        $item->likes_minus_dislikes = $likes_minus_dislikes;

        // Insert record
        try {
            $result = $db->insertObject('#__'.plgContentLikebtn::TABLE_LIKEBTN_ITEMS, $item);
        } catch (Exception $e) {}

        if (!$result) {
            // Update the record
            $result = $db->updateObject('#__'.plgContentLikebtn::TABLE_LIKEBTN_ITEMS, $item, 'identifier');
        }

        return true;
    }

    /**
     * Run locales synchronization.
     */
    /*public function runSyncLocales() {
        if ($this->timeToSync(LIKEBTN_LOCALES_SYNC_INTERVAL, 'likebtn_like_button_last_locale_sync_time')) {
            $this->syncLocales();
        }
    }*/

    /**
     * Run styles synchronization.
     */
    /*public function runSyncStyles() {
        if ($this->timeToSync(LIKEBTN_STYLES_SYNC_INTERVAL, 'likebtn_like_button_last_style_sync_time')) {
            $this->syncStyles();
        }
    }*/

    /**
     * Check if it is time to sync.
     */
    /*public function timeToSync($sync_period, $sync_variable) {

        $last_sync_time = get_option($sync_variable);

        $now = time();
        if (!$last_sync_time) {
            update_option($sync_variable, $now);
            return true;
        } else {
            if ($last_sync_time + $sync_period > $now) {
                return false;
            } else {
                update_option($sync_variable, $now);
                return true;
            }
        }
    }*/

    /**
     * Locales sync function.
     */
    /*public function syncLocales() {
        $url = LIKEBTN_API_URL . "?action=locale";

        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        if (isset($response['result']) && $response['result'] == 'success' && isset($response['response']) && count($response['response'])) {
            update_option('likebtn_like_button_locales', $response['response']);
        }
    }*/

    /**
     * Styles sync function.
     */
    /*public function syncStyles() {
        $url = LIKEBTN_API_URL . "?action=style";

        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        if (isset($response['result']) && $response['result'] == 'success' && isset($response['response']) && count($response['response'])) {
            update_option('likebtn_like_button_styles', $response['response']);
        }
    }*/

    /**
     * Reset likes/dislikes using API
     *
     * @param type $account_api_key
     * @param type $site_api_key
     */
    /*public function reset($identifier) {
        $result = false;

        $url = "identifier_filter={$identifier}";
        $response = $this->apiRequest('reset', $url);

        // check result
        if (isset($response['response']['reseted']) && $response['response']['reseted']) {
           $result = $response['response']['reseted'];
        }

        return $result;
    }*/

    /**
     * Edit likes/dislikes using API
     *
     * @param type $account_api_key
     * @param type $site_api_key
     */
    /*public function edit($identifier, $type, $value) {
        $response = $this->apiRequest('edit', "identifier_filter={$identifier}&type={$type}&value={$value}");
        return $response;
    }*/

    /**
     * Get API URL
     *
     * @param type $identifier
     * @return string
     */
    public function apiRequest($action, $request, $email = '', $api_key = '') {
        if (!self::$apiurl) {
            if (!$email) {
                $email = trim($this->plugin_params->get('email'));
            }
            if (!$api_key) {
                $api_key = trim($this->plugin_params->get('api_key'));
            }
            /*$subdirectory = trim($this->plugin_params->get('subdirectory'));
            $local_domain = trim($this->plugin_params->get('local_domain'));
            if ($local_domain) {
                $domain = $local_domain;
            } else {
                $parse_url = parse_url(plgContentLikebtn::getBaseUrl());
                $domain    = $parse_url['host'] . $subdirectory;
            }*/
            $domain_site_id = '';
            $site_id = trim($this->plugin_params->get('site_id'));
            if ($site_id) {
                $domain_site_id .= "site_id={$site_id}&";
            } else {
                $parse_url = parse_url(plgContentLikebtn::getBaseUrl());
                $domain    = $parse_url['host'];

                $domain_site_id .= "domain={$domain}&";
            }

            self::$apiurl = LIKEBTN_API_URL . "?email={$email}&api_key={$api_key}&nocache=.php&source=joomla&" . $domain_site_id;
        }
        $url = self::$apiurl . "action={$action}&" . $request;

        $response_string = $this->curl($url);
        $response = $this->jsonDecode($response_string);

        return $response;
    }

}
