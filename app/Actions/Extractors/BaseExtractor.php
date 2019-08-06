<?php

namespace App\Actions\Extractors;
use DOMElement;

class BaseExtractor
{
    public function clean($string)
    {
        $pattern = ['/"/', '/\n/'];
        $replace = ['', ' '];
        return trim(preg_replace($pattern, $replace, $string));
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
        $children  = $element->childNodes;

        foreach ($children as $child)
        {
            $html .= $element->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    public function clean_bio($string)
    {
        return strip_tags($string, '<p>');
    }
}
