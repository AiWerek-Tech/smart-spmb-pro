<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Migrations extends BaseConfig
{
    /**
     * Whether or not migrations are enabled.
     */
    public bool $enabled = true;

    /**
     * The table that stores migration information.
     */
    public string $table = 'migrations';

    /**
     * The timestamp format for migration files.
     * Options: 'timestamp' or 'sequential'
     */
    public string $timestampFormat = 'Y-m-d-His_';
}
