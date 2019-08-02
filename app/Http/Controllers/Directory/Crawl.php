<?php

namespace App\Http\Controllers\Directory;

use Illuminate\Http\Request;
use App\Actions\CrawlDirectory;


class Crawl
{
    public function __invoke(Request $request, $id)
    {
        $crawl = new CrawlDirectory($id);
        $crawl->onQueue()->execute();
        abort(200);
    }
}
