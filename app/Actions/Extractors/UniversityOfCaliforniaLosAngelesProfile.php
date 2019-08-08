<?php

namespace App\Actions\Extractors;

use DomDocument;
use App\Models\Directory;
use App\Models\DirectoryContact as Contact;

class UniversityOfCaliforniaLosAngelesProfile extends BaseExtractor
{
    public $contact;
    protected $dom;

    public function __construct(DomDocument $dom)
    {
        $this->contacts = collect([]);
        $this->dom = $dom;
    }

    public function execute()
    {
        $contact = new Contact;

        $images = $this->dom->getElementsByTagName('img');

        foreach ($images as $image) {
            if (strstr($image->parentNode->getAttribute('class'), 'bio-image')) {
                $contact->image_url = explode('?', $image->getAttribute('src'))[0] ?? '';
            }
        }

        $divs = $this->dom->getElementsByTagName('div');

        foreach ($divs as $div) {
            if (strstr($div->getAttribute('class'), 'sidearm-common-bio-full')) {
                $contact->bio = $this->clean($this->clean_bio($this->inner_html($div)));
            }
        }

        $this->contact = $contact;
    }
}
