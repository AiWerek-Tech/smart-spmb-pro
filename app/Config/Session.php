<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Session extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Session Driver
     * --------------------------------------------------------------------------
     * Use DatabaseHandler for production to store sessions in the database.
     */
    public string $driver = 'CodeIgniter\Session\Handlers\DatabaseHandler';

    /**
     * --------------------------------------------------------------------------
     * Session Cookie Name
     * --------------------------------------------------------------------------
     */
    public string $cookieName = 'ci_session';

    /**
     * --------------------------------------------------------------------------
     * Session Expiration
     * --------------------------------------------------------------------------
     * Session timeout: 60 minutes (3600 seconds) of inactivity.
     */
    public int $expiration = 3600;

    /**
     * --------------------------------------------------------------------------
     * Session Save Path
     * --------------------------------------------------------------------------
     * For DatabaseHandler: the table name to use for sessions.
     */
    public string $savePath = 'ci_sessions';

    /**
     * --------------------------------------------------------------------------
     * Session Match IP
     * --------------------------------------------------------------------------
     */
    public bool $matchIP = false;

    /**
     * --------------------------------------------------------------------------
     * Session Time to Update
     * --------------------------------------------------------------------------
     * How many seconds between CI regenerating the session ID.
     */
    public int $timeToUpdate = 300;

    /**
     * --------------------------------------------------------------------------
     * Session Regenerate Destroy
     * --------------------------------------------------------------------------
     * Destroy session data associated with the old session ID when auto-
     * regenerating the session ID.
     */
    public bool $regenerateDestroy = false;

    /**
     * --------------------------------------------------------------------------
     * Session DB Group
     * --------------------------------------------------------------------------
     * Which database group to use for the DatabaseHandler.
     */
    public ?string $DBGroup = null;
}
