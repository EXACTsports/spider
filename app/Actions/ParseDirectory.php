<?php

namespace App\Actions;

use App\Actions\ParseSidearmDirectory;

class ParseDirectory
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function execute()
    {
        // detect_provider_and_parse
        if (preg_match('/sidearmsports/', $this->body)) {
            $parser = new ParseSidearmDirectory($this->body);
        }

        // if_no_provider_try_default_parser

        $contacts = $parser->execute();

        if ($contacts->isEmpty()) {
            // if_no_contacts_log_failure
        }

    }

}
