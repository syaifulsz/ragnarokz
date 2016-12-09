<?php

namespace App\Components;

use Cocur\Slugify\Slugify;

class MangaHelper
{
    /**
     * slugify()
     *
     * @param  string $str
     * @return string
     */
    public static function slugify($str)
    {
        $slugify = new Slugify();
        $slugify->addRules(['.' => '-']);

        return $slugify->slugify($str);
    }

    /**
     * deleteDir()
     *
     * @param  string $dir
     * @return bool
     */
    public static function deleteDir($dir)
    {
        $output = false;
        if (file_exists($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::deleteDir("$dir/$file") : unlink("$dir/$file");
            }

            $output = rmdir($dir);
        }

        return $output;
    }

    /**
     * Removes params on URL
     *
     * @param  {String} $src
     * @return {String}
     */
    public static function uriRemoveParams($src)
    {
        $src = parse_url($src);
        return $src['scheme'] . '//' . $src['host'] . $src['path'];
    }

    /**
     * Removes host from URL and only retrive the path
     *
     * @param  {String} $src
     * @return {String}
     */
    public static function uriGetPath($src)
    {
        $src = parse_url($src);
        return $src['path'];
    }
}
