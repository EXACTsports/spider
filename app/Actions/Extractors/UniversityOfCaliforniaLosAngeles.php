<?php

namespace App\Actions\Extractors;

use App\Actions\Extractors\BaseExtractor;
use App\Models\Directory;
use App\Models\DirectoryContact as Contact;
use DomDocument;

class UniversityOfCaliforniaLosAngeles extends BaseExtractor
{
    public $contacts;
    public $meta;

    protected $dom;

    public function __construct(DomDocument $dom)
    {
        $this->dom = $dom;
    }

    public function execute()
    {
        $meta = collect([]);
        $contacts = collect([]);
        foreach ($this->dom->getElementsByTagName('tr') as $row) {
            $contact = new Contact;
            $headers = $row->getElementsByTagName('th');
            $cells = $row->getElementsByTagName('td');
            if (!is_null($headers[0]->getElementsByTagName('a')[0])) {
                $contact['meta'] = $headers[0]->getElementsByTagName('a')[0]->getAttribute('aria-label') ?? '';
                $contact['profile_url'] = $headers[0]->getElementsByTagName('a')[0]->getAttribute('href') ?? '';
            }
            if (preg_match('/phone/i', $headers[0]->textContent)) {
                $meta->push($this->clean($headers[0]->textContent));

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

        $this->meta = $meta;
        $this->contacts = $contacts;
    }
}
