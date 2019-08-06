<?php

namespace App\Actions;

use DomDocument;
use Symfony\Component\ClassLoader\ClassMapGenerator;
class ParseDirectory
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
        $this->tidy_config = ['clean' => 'yes', 'output-html' => 'yes'];
        $extractors = [];
        foreach (glob(__DIR__.'/Extractors/*.php') as $file) {
            $extractors[] = basename($file, '.php');
        }
        $this->extractors = $extractors;

        foreach ($this->extractors as $key) {
            $results[$key] = $key($dom, $directory);
        }
    }

    public function execute()
    {
        $html = tidy_parse_string($this->body, $this->tidy_config, 'utf8');
dd($this->extractors);
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);

        $dom->loadHTML($html);



        $scraping_methods = [
            'sidearm_use_aria_as_metadata'
        ];
        $results = [];

        foreach ($scraping_methods as $method) {
            $contacts = self::$method($dom);
            $coaches = self::filter_coaches($contacts);

            $results[$method] = ['contacts' => $contacts, 'coaches' => self::filter_coaches($coaches), 'list_quality' => self::score_quality_of_coaches_array($coaches)];
        }



    }

    /** Start Scraping Methods */
    private static function sidearm_use_aria_as_metadata(DomDocument $dom)
    {
        $rows = $dom->getElementsByTagName('tr');
        $contacts = [];
        foreach ($rows as $row) {
            if ($row->getAttribute('data-member-id')) {
                $contact = [];
                $headers = $row->getElementsByTagName('th');
                $contact['meta'] = $headers[0]->getElementsByTagName('a')[0]->getAttribute('aria-label') ?? '';
                $contact['name'] = self::clean($headers[0]->getElementsByTagName('a')[0]->textContent ?? '');
                $cells = $row->getElementsByTagName('td');
                foreach ($cells as $cell) {
                    if (self::extract_name($cell) && !isset($contact['name'])) {
                        $contact['name'] = self::extract_name($cell);
                    }
                    if (self::extract_phone($cell)) {
                        $contact['phone'] = self::extract_phone($cell);
                    }
                    if (self::extract_email($cell)) {
                        $contact['email'] = self::extract_email($cell);
                    }
                    if (self::extract_title($cell)) {
                        $contact['title'] = self::extract_title($cell);
                    }
                }
                $contacts[] = $contact;
            }
        }

        return collect($contacts);
    }

    /** Key Extraction Methods */
    private static function extract_phone($cell)
    {
        $links = $cell->getElementsByTagName('a');
        foreach ($links as $link) {
            if ($link->hasAttribute('href') && strstr($link->getAttribute('href'), 'tel:')) {
                return preg_replace('/tel:/i', $link->getAttribute('href'));
            }
        }
        if (strlen(preg_replace('/[^0-9]/', '', $cell->textContent)) == 10) {
            return preg_replace('/[^0-9]/', '', $cell->textContent);
        }
        return false;
    }

    private static function extract_name($cell)
    {
        if (self::is_human_name($cell->textContent) && !self::extract_title($cell)) {
            return trim($cell->textContent);
        }
        return false;
    }

    private static function extract_email($cell)
    {
        $links = $cell->getElementsByTagName('a');
        foreach ($links as $link) {
            if ($link->hasAttribute('href') && strstr($link->getAttribute('href'), 'mailto:')) {
                return trim(strtolower(preg_replace('/mailto:/i', '', $link->getAttribute('href'))));
            }
        }

        if (filter_var(trim($cell->textContent), FILTER_VALIDATE_EMAIL)) {
            return trim(strtolower($cell->textContent));
        }

        return false;

    }

    private static function extract_title($cell)
    {
        $title_keys = ['/coach/i', '/assistant/i', '/director/i', '/volunteer/i', '/scouting/i', '/coordinator/i'];


        foreach ($title_keys as $key) {
            if (preg_match($key, $cell->textContent)) {
                return self::clean(trim($cell->textContent));
            }
        }

        return false;
    }

    private static function extract_team_sport($contact)
    {

    }

    private static function extract_team_gender($contact)
    {

    }

    /** Cleaning / filtering */

    private static function clean($string)
    {
        $pattern = ['/"/', '/\n/'];
        $replace = ['', ' '];
        return trim(preg_replace($pattern, $replace, $string));
    }

    private static function is_human_name($string)
    {
        $title_keys = ['/coach/i', '/assistant/i', '/director/i', '/volunteer/i', '/scouting/i', '/coordinator/i'];

        foreach ($title_keys as $key) {
            $string = preg_replace($key, '', $string);
        }
        $nar = explode(' ', $string);

        if (sizeof($nar) < 2 || sizeof($nar) > 4) {
            return false;
        }
        return true;
    }


    private static function filter_coaches($contacts)
    {
        return $contacts;
    }

    private static function score_quality_of_coaches_array($coaches)
    {
        return 0;
    }
}
