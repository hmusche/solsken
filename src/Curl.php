<?php

namespace Solsken;

/**
 * Wrapper class for calling URLs via CURL and decode content
 */
class Curl {
    protected $_client;

    /**
     * Possible options for __construct and their default values
     * @var array
     */
    protected $_options = [
        'url'    => null,
        'format' => 'json'
    ];

    /**
     * Get the client and set the options
     * @param array $options  See $this->_options
     */
    public function __construct($options = []) {
        $this->_client = curl_init();

        curl_setopt($this->_client, CURLOPT_RETURNTRANSFER, 1);

        $this->_options = array_merge($this->_options, $options);
    }

    public function setPostData($data) {
        curl_setopt($this->_client, CURLOPT_POST, 1);
        curl_setopt($this->_client, CURLOPT_POSTFIELDS, http_build_query($data));

        return $this;
    }

    /**
     * Call the optionally given URL, otherwise the URL statet in options, and return the content
     * @param  String $url Optional URL to call
     * @return Mixed       Return of call, can be formatted by "format" option, false if an error occured
     */
    public function call($url = null) {
        if ($url === null) {
            $url = $this->_options['url'];
        }

        curl_setopt($this->_client, CURLOPT_URL, $url);

        $data = curl_exec($this->_client);

        if (curl_errno($this->_client)) {
            return false;
        }

        if (isset($this->_options['format'])) {
            switch ($this->_options['format']) {
                case 'json':
                    $data = json_decode($data, true);
                    break;
            }
        }

        return $data;
    }
}
