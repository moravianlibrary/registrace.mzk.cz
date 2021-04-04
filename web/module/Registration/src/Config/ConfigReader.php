<?php

declare(strict_types=1);

namespace Registration\Config;

class ConfigReader
{

    public function getConfig($filename)
    {
        $file = APPLICATION_PATH . '/data/registration/' . $filename;
        $reader = new \Laminas\Config\Reader\Ini();
        $reader->setTypedMode(false);
        return $reader->fromFile($file);
    }

}