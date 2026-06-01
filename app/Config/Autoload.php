<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * -------------------------------------------------------------------
 * AUTO-LOADER
 * -------------------------------------------------------------------
 * This file defines the namespaces and class maps so the Autoloader
 * can find the files as needed.
 */
class Autoload extends AutoloadConfig
{
    /**
     * An array of namespaces to be auto-discovered.
     *
     * @var array<string, array<int, string>|string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
        'Config'      => APPPATH . 'Config',
    ];

    /**
     * An array of class names mapped to file paths.
     *
     * @var array<string, string>
     */
    public $classmap = [];

    /**
     * An array of files that should always be loaded.
     *
     * @var list<string>
     */
    public $files = [];

    /**
     * An array of helper files to be loaded on every request.
     *
     * @var list<string>
     */
    public $helpers = [];
}
