<?php

namespace App\Actions;

class ListSidearmDirectories
{
    protected $import_file;

    public function __construct()
    {
        $this->import_file = storage_path('importers/sidearm_sports.json');
    }

    public function execute()
    {
        return collect(json_decode(file_get_contents($this->import_file), true)['sites'] ?? []);
    }
}
