<?php


namespace AmberCore\Helper;


trait FileWorkTrait
{
    protected function checkDir(string $path)
    {
        if (!file_exists(dirname($path)))
        {
            mkdir(dirname($path), 0644, true);
        }
    }
}