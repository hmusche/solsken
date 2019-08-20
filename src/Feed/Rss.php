<?php

namespace Solsken\Feed;

class Rss extends FeedAbstract {
    protected $_rootName = 'rss';

    protected $_xmlHeaderString = '<?xml version="1.0"?><rss version="2.0"></rss>';

    protected $_optionalElements = [
        'language' => [
            'type' => 'single'
        ],
        'copyright' => [
            'type' => 'single'
        ],
        'pubDate' => [
            'type' => 'single'
        ],
        'category' => [
            'type' => 'single'
        ],
        'ttl' => [
            'type' => 'single'
        ],
        'image' => [
            'type' => 'single'
        ]
    ];

    public function setImage($url, $title = null, $link = null) {
        if ($title === null) {
            $title = $this->_elements['title'];
        }

        if ($link === null) {
            $link = $this->_elements['link'];
        }

        $this->_elements['image'] = [
            'url'   => $url,
            'title' => $title,
            'link'  => $link
        ];

        return $this;
    }

    public function render() {
        return $this->getDom()->saveXml();
    }
}
