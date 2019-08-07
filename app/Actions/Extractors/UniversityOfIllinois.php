<?php

namespace App\Actions\Extractors;

use App\Models\Directory;
use App\Models\DirectoryContact as Contact;
use DomDocument;

class UniversityOfIllinois extends BaseExtractor
{
    public $contacts;
    protected $dom;

    public function __construct(DomDocument $dom)
    {
        $this->contacts = collect([]);
        $this->dom = $dom;
    }

    public function execute()
    {
        $sports = [];
        $contacts = collect([]);
        foreach ($this->dom->getElementsByTagName('script') as $script) {
            if (preg_match('/loadRow/', $script->textContent)) {
                $lrs = explode("\n", $script->textContent);
                foreach ($lrs as $line) {
                    $line = preg_replace('/loadRow\(/', '', $line);
                    $line = preg_replace('/\);/', '', $line);
                    $line = preg_replace('/\'/', '', $line);
                    $sports[] = explode(', ', $line);

                }
            }
        }

        $blocks = [];
        foreach ($sports as $sport) {
            if (sizeof($sport) > 3) {
                $blocks[$sport[3]] = $sport[0];
            }
        }


        $i = -1;
        $meta = 'Administration';
        foreach ($this->dom->getElementsByTagName('table') as $table) {

            if ($table->getAttribute('id') == 'ctl00_cplhMainContent_dgrdStaff') {
                foreach ($table->getElementsByTagName('tr') as $row) {
                    $meta = isset($blocks[$i]) ? $blocks[$i] : $meta;

                    $i++;
                    $contact = new Contact;
                    $contact['meta'] = $meta;
                    $cells = $row->getElementsByTagName('td');
                    foreach ($cells as $cell) {
                        $links = $row->getElementsByTagName('a');

                        foreach ($links as $link) {
                            if ($link->hasAttribute('href') && strstr($link->getAttribute('href'), 'mailto:')) {
                                $contact->email = trim(strtolower(preg_replace('/mailto:/i', '', $link->getAttribute('href'))));
                            }
                            if ($link->hasAttribute('href') && strstr($link->getAttribute('href'), 'tel:')) {
                                $contact->phone = preg_replace('/tel:/i', $link->getAttribute('href'));
                            }
                        }
                        if (preg_match('/fullname/', $cell->getAttribute('class'))) {
                            $contact->name = $this->clean($cell->textContent);
                        }
                        if (preg_match('/staff_dgrd_staff_title/', $cell->getAttribute('class'))) {
                            $contact->title = $this->clean($cell->textContent);
                        }
                        if (preg_match('/staff_dgrd_staff_phone/', $cell->getAttribute('class'))) {
                            $contact->phone = $this->clean_phone($cell->textContent);
                        }
                    }

                    if (isset($contact->email) && !empty($contact->email)) {
                        $contacts->push($contact);
                    }

                }
            }
        }


        $this->contacts = $contacts;

    }
}
