<?php

namespace App\Actions;

use DomDocument;

class ExtractContacts
{
    protected $html;

    public function __construct($html)
    {
        $this->html = $html;
    }
}
