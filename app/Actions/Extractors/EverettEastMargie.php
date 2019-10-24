<?php

namespace App\Actions\Extractors;

use App\Actions\FullyQualify;
use App\Models\Directory;
use App\Models\DirectoryContact as Contact;
use DomDocument;

/**
 * Do not edit or remove comment:
 * Extractor Based On: https://uclabruins.com/staff.aspx?staff=542
 */

class EverettEastMargie extends BaseExtractor
{
    public $contacts;
    protected $dom;

    public function __construct(DomDocument $dom, $url = null)
    {
        $this->contacts = collect([]);
        $this->dom = $dom;
        FullyQualify::transform($dom, $url);
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
