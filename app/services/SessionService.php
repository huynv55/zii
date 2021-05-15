<?php
class SessionService {
    const SESSION_NAME = 'blog_session';
    const SESSION_FLASH_NAME = 'blog_flash_session';
    const SESSION_LIFT_TIME = 86400;
    /**
     * Determine if session has started.
     *
     * @var bool
     */
    private static $sessionStarted = false;

    public static function init($lifeTime = null)
    {
        $timeout = !empty($lifeTime) ? $lifeTime : self::SESSION_LIFT_TIME;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::$sessionStarted = session_start([
                'cookie_lifetime' => $timeout
            ]);
        }
        return self::$sessionStarted;
    }

    /**
     * Get session id.
     *
     * @return string → the session id or empty
     */
    public static function id()
    {
        return session_id();
    }

    /**
     * Regenerate session_id.
     *
     * @return string → session_id
     */
    public static function regenerate()
    {
        session_regenerate_id(true);
        return session_id();
    }

    public static function put($key, $data) {
        if(empty($_SESSION[self::SESSION_NAME])) {
            $_SESSION[self::SESSION_NAME] = [];
        }
        $_SESSION[self::SESSION_NAME][$key] = $data;
    }

    public static function get($key) {
        if(!empty($_SESSION[self::SESSION_NAME][$key])) {
            return $_SESSION[self::SESSION_NAME][$key];
        } else {
            return null;
        }
    }

    public static function remove($key) {
        if(!empty($_SESSION[self::SESSION_NAME][$key])) {
            unset($_SESSION[self::SESSION_NAME][$key]);
        }
    }

    public static function destroy($destroy_all = false) {
        if (self::$sessionStarted == true) {
            if ($destroy_all) {
                session_unset();
                session_destroy();
            } else {
                if(!empty($_SESSION[self::SESSION_NAME])) {
                    unset($_SESSION[self::SESSION_NAME]);
                }
            }
            return true;
        }
        return false;
    }

    public static function putFlashData($key, $data) {
        if(empty($_SESSION[self::SESSION_FLASH_NAME])) {
            $_SESSION[self::SESSION_FLASH_NAME] = [];
        }
        $_SESSION[self::SESSION_FLASH_NAME][$key] = $data;
    }

    public static function getFlashData($key) {
        if(!empty($_SESSION[self::SESSION_FLASH_NAME][$key])) {
            $res = $_SESSION[self::SESSION_FLASH_NAME][$key];
            unset($_SESSION[self::SESSION_FLASH_NAME][$key]);
            return $res;
        } else {
            return null;
        }
    }

    public static function pull($key)
    {
        $value = self::get($key);
        self::remove($key);
        return $value;
    }
}
?>