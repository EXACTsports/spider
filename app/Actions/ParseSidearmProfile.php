<?php

namespace App\Actions;

class ParseSidearmProfile
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
    }

    public function execute()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);

        $dom->loadHTML($html);

        $contact = [];

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            if ($image->parentNode->hasAttribute('class') && strstr($image->parentNode->getAttribute('class'), 'sidearm-staff-member-bio-image')) {
                $contact['image_url'] = $image->getAttribute('src');
            }
        }

        return $contact;
    }
}
