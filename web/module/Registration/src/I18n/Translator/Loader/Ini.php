<?php

/**
 * @see       https://github.com/laminas/laminas-i18n for the canonical source repository
 * @copyright https://github.com/laminas/laminas-i18n/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-i18n/blob/master/LICENSE.md New BSD License
 */

namespace Registration\I18n\Translator\Loader;

use Laminas\Config\Reader\Ini as IniReader;
use Laminas\I18n\Exception;
use Laminas\I18n\Translator\Loader\AbstractFileLoader;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Laminas\I18n\Translator\TextDomain;


/**
 * PHP INI format loader.
 */
class Ini extends AbstractFileLoader
{
    /**
     * load(): defined by FileLoaderInterface.
     *
     * @see    FileLoaderInterface::load()
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load($locale, $filename)
    {
        $resolvedIncludePath = stream_resolve_include_path($filename);
        $fromIncludePath = ($resolvedIncludePath !== false) ? $resolvedIncludePath : $filename;
        if (! $fromIncludePath || ! is_file($fromIncludePath) || ! is_readable($fromIncludePath)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename
            ));
        }

        $iniReader = new IniReader();
        $messages = $iniReader->fromFile($fromIncludePath);
        return new TextDomain($messages);
    }
}
