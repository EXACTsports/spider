<?php

namespace App\Actions\Extractors;

use App\Models\Directory;
use App\Models\DirectoryContact as Contact;
use DomDocument;

/**
 * Do not edit or remove comment:
 * Extractor Based On: https://uclabruins.com/staff.aspx
 */

class IsacWymanside extends BaseExtractor
{
    public $contacts;
    protected $dom;

    public function __construct(DomDocument $dom, $url = null)
    {
        $this->contacts = collect([]);
        $this->dom = $dom;
        $this->url = $url;
        $this->fullyQualify();
    }

    public function execute()
    {
        $team_phones = [];
        $contacts = collect([]);
        foreach ($this->dom->getElementsByTagName('tr') as $row) {
            $contact = new Contact;
            $headers = $row->getElementsByTagName('th');
            $cells = $row->getElementsByTagName('td');
            if (! is_null($headers[0]->getElementsByTagName('a')[0])) {
                $contact['meta'] = $headers[0]->getElementsByTagName('a')[0]->getAttribute('aria-label') ?? '';
                $contact['profile_url'] = $headers[0]->getElementsByTagName('a')[0]->getAttribute('href') ?? '';
            }
            if (preg_match('/phone/i', $headers[0]->textContent)) {
                array_push($team_phones, $this->clean($headers[0]->textContent));
            }
            $contact['name'] = $this->clean($headers[0]->getElementsByTagName('a')[0]->textContent ?? '');
            foreach ($cells as $cell) {
                $links = $cell->getElementsByTagName('a');
                foreach ($links as $link) {
                    if ($link->hasAttribute('href') && strstr($link->getAttribute('href'), 'mailto:')) {
                        $contact->email = trim(strtolower(preg_replace('/mailto:/i', '', $link->getAttribute('href'))));
                    }
                    if ($link->hasAttribute('href') && strstr($link->getAttribute('href'), 'tel:')) {
                        $contact->phone = preg_replace('/tel:/i', $link->getAttribute('href'));
                    }
                }
                if ($this->is_title($cell->textContent)) {
                    $contact->title = $this->clean($cell->textContent);
                }
            }

            $contacts->push($contact);
        }

        $this->meta = collect(['team_phones' => $team_phones]);
        $this->contacts = $contacts;
    }
}
