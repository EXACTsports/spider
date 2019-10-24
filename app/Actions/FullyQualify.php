<?php

namespace App\Actions;

use DOMDocument;
use webignition\Uri\Uri;
use webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver;

class FullyQualify
{
    public static function transform(DomDocument $dom, $url = null): void
    {
        if (is_null($url)) {
            return;
        }
        $up = parse_url($url);

        $scheme = $up['scheme'] ?? 'https';
        $base_url = $scheme.'://'.$up['host'];

        foreach ($dom->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');

            if (! preg_match('/mailto:/', $href) && ! preg_match('/tel:/', $href)) {
                $link->setAttribute('href', AbsoluteUrlDeriver::derive(new Uri($base_url), new Uri($href)));
            }
        }

        foreach ($links = $dom->getElementsByTagName('link') as $link) {
            $href = $link->getAttribute('href');

            if (! preg_match('/mailto:/', $href) && ! preg_match('/tel:/', $href)) {
                $link->setAttribute('href', AbsoluteUrlDeriver::derive(new Uri($base_url), new Uri($href)));
            }
        }

        foreach ($dom->getElementsByTagName('img') as $image) {
            $src = $image->getAttribute('src');

            if (! preg_match('/data:/', $src)) {
                $image->setAttribute('src', AbsoluteUrlDeriver::derive(new Uri($base_url), new Uri($src)));
            }
        }
    }
}
