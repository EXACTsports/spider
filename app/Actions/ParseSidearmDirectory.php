<?php

namespace App\Actions;

class ParseSidearmDirectory
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

        $rows = $dom->getElementsByTagName('tr');

        $contacts = [];
        foreach ($rows as $row) {
            if ($row->getAttribute('data-member-id')) {
                $headers = $row->getElementsByTagName('th');
                $meta = $headers[0]->getElementsByTagName('a')[0]->getAttribute('aria-label');
                $cells = $row->getElementsByTagName('td');
                $contacts[] = [
                    'name' => self::clean($headers[0]->nodeValue),
                    'title' => self::clean($cells[0]->nodeValue),
                    'email' => self::clean($cells[1]->nodeValue),
                    'meta' => $meta
                ];
            }
        }

        return collect($contacts);
    }

    private static function clean($string)
    {
        $pattern = ['/"/', '/\n/'];
        $replace = ['', ' '];
        return trim(preg_replace($pattern, $replace, $string));
    }
}
