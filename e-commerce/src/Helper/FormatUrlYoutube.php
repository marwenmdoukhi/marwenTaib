<?php

namespace App\Helper;

class FormatUrlYoutube
{
    /**
     * @param string|null $url
     * @return string
     */
    public static function format(?string $url): string
    {
        if (true === isset($url)) {
            $lien = str_replace("watch?v=", "embed/", $url);
            return str_replace("youtu.be/", "www.youtube.com/embed/", $lien);
        }
        return "";
    }
}
