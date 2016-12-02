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
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::deleteDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
