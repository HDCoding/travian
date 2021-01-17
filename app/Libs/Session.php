<?php

namespace Travian\Libs;

class Session
{
    public $db;

    public $checker, $mchecker, $villages = [], $bonus = 0, $bonus1 = 0, $bonus2 = 0, $bonus3 = 0, $bonus4 = 0, $username,
        $uid, $access, $plus, $tribe, $isAdmin, $alliance, $gold, $oldrank, $gpack, $referrer, $url, $logged_in = false, $is_sitter;
    public $goldclub;
    public $userinfo = [];
    private $time;
    private $userarray = [];
    public $silver;
    public $email;

    public function __construct()
    {
        $this->checkIP();
        $this->db = Database::getInstance();

        $this->time = $_SERVER['REQUEST_TIME'];
        self::startSession();

        $this->logged_in = $this->checkLogin();

        if (self::get('lang') || self::get('lang') == '') {
            self::set('lang', constant('LANG'));
        }

        if ($this->logged_in) {

        }

    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * Start session.
     */
    public static function startSession()
    {
        ini_set("session.use_only_cookies", SESSION_USE_ONLY_COOKIES);
        //ini_set('session.cookie_domain', '.localhost');

        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(
            $cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            SESSION_SECURE,
            SESSION_HTTP_ONLY
        );
        //session_name(SNAME);

        session_start();
        session_regenerate_id(SESSION_REGENERATE_ID);
    }

    /**
     * Destroy session.
     */
    public static function destroySession()
    {
        $_SESSION = [];

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 420000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
        session_destroy();
    }

    /**
     * Set session data.
     * @param mixed $key Key that will be used to store value.
     * @param mixed $value Value that will be stored.
     * @return mixed
     */
    public static function set($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * Get data from $_SESSION variable.
     * @param mixed $key Key used to get data from session.
     * @param mixed $default This will be returned if there is no record inside
     * session for given key.
     * @return mixed Session value for given key.
     */
    public static function get($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    /**
     * Unset session data with provided key.
     * @param $key
     */
    public static function destroy($key)
    {
        if ( isset($_SESSION[$key]) ) {
            unset($_SESSION[$key]);
        }
    }

    public static function flash($name, $message = "")
    {
        if (self::get($name)) {
            $session = self::get($name);
            self::destroy($name);
        } else {
            self::set($name, $message);
        }
        return $session;
    }

    public function checkIP()
    {
        $file = getcwd() . '../../cache/blacklist.txt';

        if (file_exists($file)) {
            $list = file($file);
            foreach ($list as $addr) {
                $addr = trim($addr);
                $host_addr = Helper::getIP();
                // Simple IP address
                if ($host_addr == $addr) {
                    die(US_BANIPMSG);
                }
                // Class C subnet
                else if (preg_match('/(\d+\.\d+\.\d+)\.0\/24/', $addr, $sub)) {
                    $subnet = trim($sub[1]);
                    if (preg_match("/^{$subnet}/", $host_addr)) {
                        die(US_BANIPMSG);
                    }
                }
                // Class B subnet
                else if (preg_match('/(\d+\.\d+)\.0\.0\/16/', $addr, $sub)) {
                    $subnet = trim($sub[1]);
                    if (preg_match("/^{$subnet}/", $host_addr)) {
                        die(US_BANIPMSG);
                    }
                }
                // Class A subnet
                else if (preg_match('/(\d+)\.0\.0\.0\/8/', $addr, $sub)) {
                    $subnet = trim($sub[1]);
                    if (preg_match("/^{$subnet}/", $host_addr)) {
                        die(US_BANIPMSG);
                    }
                }
            }
        }
    }
    
}