<?php

namespace Solsken;

class Curl {
    protected $_client;

    protected $_options = [
        'url' => null
    ];

    public function __construct($options = []) {
        $this->_client = curl_init();

        curl_setopt($this->_client, CURLOPT_RETURNTRANSFER, 1);

        $this->_options = array_merge($this->_options, $options);
    }

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
