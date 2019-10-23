<?php

namespace App\Actions\Extractors;

use DOMElement;
use webignition\AbsoluteUrlDeriver\AbsoluteUrlDeriver;
use webignition\Uri\Uri;

class BaseExtractor
{
    public function clean($string)
    {
        $pattern = ['/"/', '/\n/'];
        $replace = ['', ' '];

        return trim(preg_replace($pattern, $replace, $string));
    }

    public function clean_phone($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }

    public function is_title($string)
    {
        $title_keys = ['/coach/i', '/assistant/i', '/director/i', '/volunteer/i', '/scouting/i', '/coordinator/i'];

        foreach ($title_keys as $key) {
            if (preg_match($key, $string)) {
                return true;
            }
        }

        return false;
    }

    public function inner_html(DOMElement $element)
    {
        $html = '';
        $children = $element->childNodes;

        foreach ($children as $child) {
            $html .= $element->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    public function clean_bio($string)
    {
        return strip_tags($string, '<p>');
    }

    public function fullyQualify()
    {
        if (is_null($this->url)) {
            return;
        }
        $up = parse_url($this->url);



        $scheme = $up['scheme'] ?? 'https';
        $this->base_url = $scheme . '://' . $up['host'];


        $links = $this->dom->getElementsByTagName('a');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            if (!preg_match('/mailto:/', $href) && !preg_match('/tel:/', $href)) {
                $link->setAttribute('href', AbsoluteUrlDeriver::derive(new Uri($this->base_url), new Uri($href)));
            }
        }

        $images = $this->dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $src = $image->getAttribute('src');

            if (!preg_match('/data:/', $src)) {
                $image->setAttribute('src', AbsoluteUrlDeriver::derive(new Uri($this->base_url), new Uri($src)));
            }
        }
    }
}
