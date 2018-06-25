<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System;

// ------------------------------------------------------------------------

use O2System\Psr\Log\LoggerAwareInterface;
use O2System\Psr\Log\LoggerInterface;
use O2System\Session\Abstracts\AbstractHandler;
use O2System\Spl\Iterators\ArrayIterator;
use Traversable;

/**
 * Class Session
 *
 * @package O2System
 */
class Session implements \ArrayAccess, \IteratorAggregate, LoggerAwareInterface
{
    /**
     * Session Config
     *
     * @var Kernel\Datastructures\Config
     */
    protected $config;

    /**
     * Logger Instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Session Cache Platform Handler
     *
     * @var AbstractHandler
     */
    protected $handler;

    protected $sidRegexp;

    // ------------------------------------------------------------------------

    /**
     * Session::__construct
     *
     * @param Kernel\Datastructures\Config $config
     *
     * @return Session
     */
    public function __construct(Kernel\Datastructures\Config $config)
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('session');

        $this->config = $config;

        if ($this->config->offsetExists('handler')) {
            $handlerClassName = '\O2System\Session\Handlers\\' . ucfirst($this->config->handler) . 'Handler';

            if (class_exists($handlerClassName)) {
                $this->handler = new $handlerClassName(clone $this->config);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::isSupported
     *
     * Checks if server is support cache storage platform.
     *
     * @param string $platform Platform name.
     *
     * @return bool
     */
    public static function isSupported($platform)
    {
        $handlerClassName = '\O2System\Session\Handlers\\' . ucfirst($platform) . 'Handler';

        if (class_exists($handlerClassName)) {
            return (new $handlerClassName)->isSupported();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::setLogger
     *
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger =& $logger;

        // Load Session Language
        language()->loadFile('session');

        if (isset($this->handler)) {
            $this->handler->setLogger($this->logger);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::start
     *
     * Initialize Native PHP Session.
     *
     * @return void
     */
    public function start()
    {
        if (php_sapi_name() === 'cli') {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->debug('DEBUG_SESSION_CLI_ABORTED');
            }

            return;
        } elseif ((bool)ini_get('session.auto_start')) {
            if ($this->logger instanceof LoggerInterface) {
                $this->logger->error('DEBUG_SESSION_AUTO_START_ABORTED');
            }

            return;
        }

        if ( ! $this->handler instanceof \SessionHandlerInterface) {
            $this->logger->error('E_SESSION_HANDLER_INTERFACE', [$this->handler->getPlatform()]);
        }

        $this->setConfiguration();

        session_set_save_handler($this->handler, true);

        // Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers
        if (isset($_COOKIE[ $this->config[ 'name' ] ]) && (
                ! is_string($_COOKIE[ $this->config[ 'name' ] ]) || ! preg_match('#\A' . $this->sidRegexp . '\z#',
                    $_COOKIE[ $this->config[ 'name' ] ])
            )
        ) {
            unset($_COOKIE[ $this->config[ 'name' ] ]);
        }

        /* Check if session is activated or not yet */
        if ( ! $this->isStarted()) {
            session_start();
        }

        // Is session ID auto-regeneration configured? (ignoring ajax requests)
        if ((empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) ||
                strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) !== 'xmlhttprequest') &&
            ($regenerateTime = $this->config[ 'regenerate' ]->lifetime) > 0
        ) {
            if ( ! isset($_SESSION[ '__o2sessionLastRegenerate' ])) {
                $_SESSION[ '__o2sessionLastRegenerate' ] = time();
            } elseif ($_SESSION[ '__o2sessionLastRegenerate' ] < (time() - $regenerateTime)) {
                $this->regenerate((bool)$this->config[ 'regenerate' ]->destroy);
            }
        }
        // Another work-around ... PHP doesn't seem to send the session cookie
        // unless it is being currently created or regenerated
        elseif (isset($_COOKIE[ $this->config[ 'name' ] ]) && $_COOKIE[ $this->config[ 'name' ] ] === session_id()
        ) {
            setcookie(
                $this->config[ 'name' ],
                session_id(),
                (empty($this->config[ 'lifetime' ]) ? 0 : time() + $this->config[ 'lifetime' ]),
                $this->config[ 'cookie' ]->path,
                $this->config[ 'cookie' ]->domain,
                $this->config[ 'cookie' ]->secure,
                $this->config[ 'cookie' ]->httpOnly
            );
        }

        $this->initializeVariables();

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->debug('DEBUG_SESSION_INITIALIZED', [$this->handler->getPlatform()]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Session::setConfiguration
     *
     * Handle input binds and configuration defaults.
     *
     * @return void
     */
    private function setConfiguration()
    {
        if (empty($this->config[ 'name' ])) {
            $this->sessionCookieName = ini_get('session.name');
        } else {
            ini_set('session.name', $this->config[ 'name' ]);
        }

        if (empty($this->config[ 'lifetime' ])) {
            $this->config[ 'lifetime' ] = (int)ini_get('session.gc_maxlifetime');
        } else {
            ini_set('session.gc_maxlifetime', (int)$this->config[ 'lifetime' ]);
        }

        if (empty($this->config[ 'cookie' ]->domain)) {
            $this->config[ 'cookie' ]->domain = (isset($_SERVER[ 'HTTP_HOST' ]) ? $_SERVER[ 'HTTP_HOST' ]
                : (isset($_SERVER[ 'SERVER_NAME' ]) ? $_SERVER[ 'SERVER_NAME' ] : null));
        }

        $this->config[ 'cookie' ]->domain = '.' . ltrim($this->config[ 'cookie' ]->domain, '.');

        session_set_cookie_params(
            $this->config[ 'cookie' ]->lifetime,
            $this->config[ 'cookie' ]->path,
            $this->config[ 'cookie' ]->domain,
            $this->config[ 'cookie' ]->secure,
            $this->config[ 'cookie' ]->httpOnly
        );

        // Security is king
        ini_set('session.use_trans_sid', 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        $this->configureSidLength();
    }

    //--------------------------------------------------------------------

    /**
     * Configure session ID length
     *
     * To make life easier, we used to force SHA-1 and 4 bits per
     * character on everyone. And of course, someone was unhappy.
     *
     * Then PHP 7.1 broke backwards-compatibility because ext/session
     * is such a mess that nobody wants to touch it with a pole stick,
     * and the one guy who does, nobody has the energy to argue with.
     *
     * So we were forced to make changes, and OF COURSE something was
     * going to break and now we have this pile of shit. -- Narf
     *
     * @return    void
     */
    protected function configureSidLength()
    {
        if (PHP_VERSION_ID < 70100) {
            $bits = 160;
            $hash_function = ini_get('session.hash_function');
            if (ctype_digit($hash_function)) {
                if ($hash_function !== '1') {
                    ini_set('session.hash_function', 1);
                    $bits = 160;
                }
            } elseif ( ! in_array($hash_function, hash_algos(), true)) {
                ini_set('session.hash_function', 1);
                $bits = 160;
            } elseif (($bits = strlen(hash($hash_function, 'dummy', false)) * 4) < 160) {
                ini_set('session.hash_function', 1);
                $bits = 160;
            }
            $bits_per_character = (int)ini_get('session.hash_bits_per_character');
            $sid_length = (int)ceil($bits / $bits_per_character);
        } else {
            $bits_per_character = (int)ini_get('session.sid_bits_per_character');
            $sid_length = (int)ini_get('session.sid_length');
            if (($sid_length * $bits_per_character) < 160) {
                $bits = ($sid_length * $bits_per_character);
                // Add as many more characters as necessary to reach at least 160 bits
                $sid_length += (int)ceil((160 % $bits) / $bits_per_character);
                ini_set('session.sid_length', $sid_length);
            }
        }
        // Yes, 4,5,6 are the only known possible values as of 2016-10-27
        switch ($bits_per_character) {
            case 4:
                $this->sidRegexp = '[0-9a-f]';
                break;
            case 5:
                $this->sidRegexp = '[0-9a-v]';
                break;
            case 6:
                $this->sidRegexp = '[0-9a-zA-Z,-]';
                break;
        }
        $this->sidRegexp .= '{' . $sid_length . '}';
    }

    //--------------------------------------------------------------------

    /**
     * Session::isStarted
     *
     * Check if the PHP Session is has been started.
     *
     * @access  public
     * @return  bool
     */
    public function isStarted()
    {
        if (php_sapi_name() !== 'cli') {
            return session_status() == PHP_SESSION_NONE ? false : true;
        }

        return false;
    }

    /**
     * Session::regenerate
     *
     * Regenerates the session ID.
     *
     * @param bool $destroy Should old session data be destroyed?
     *
     * @return void
     */
    public function regenerate($destroy = false)
    {
        $_SESSION[ '__o2sessionLastRegenerate' ] = time();
        session_regenerate_id($destroy);
    }

    //--------------------------------------------------------------------

    /**
     * Session::initializeVariables
     *
     * Handle flash and temporary session variables. Clears old "flash" session variables,
     * marks the new one for deletion and handles "temp" session variables deletion.
     *
     * @return void
     */
    private function initializeVariables()
    {
        if ( ! empty($_SESSION[ '__o2sessionVariables' ])) {
            $currentTime = time();

            foreach ($_SESSION[ '__o2sessionVariables' ] as $key => &$value) {
                if ($value === 'new') {
                    $_SESSION[ '__o2sessionVariables' ][ $key ] = 'old';
                }
                // Hacky, but 'old' will (implicitly) always be less than time() ;)
                // DO NOT move this above the 'new' check!
                elseif ($value < $currentTime) {
                    unset($_SESSION[ $key ], $_SESSION[ '__o2sessionVariables' ][ $key ]);
                }
            }

            if (empty($_SESSION[ '__o2sessionVariables' ])) {
                unset($_SESSION[ '__o2sessionVariables' ]);
            }
        }
    }

    //--------------------------------------------------------------------

    /**
     * Does a full stop of the session:
     *
     * - destroys the session
     * - unsets the session id
     * - destroys the session cookie
     */
    public function stop()
    {
        setcookie(
            $this->config[ 'name' ],
            session_id(),
            1,
            $this->config[ 'cookie' ]->path,
            $this->config[ 'cookie' ]->domain,
            $this->config[ 'cookie' ]->secure,
            $this->config[ 'cookie' ]->httpOnly
        );

        session_regenerate_id(true);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::destroy
     *
     * Destroys the current session.
     *
     * @return void
     */
    public function destroy()
    {
        session_destroy();
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__isset
     *
     * Implementing magic method __isset to simplify when checks if offset exists on PHP native session variable,
     * just simply calling isset( $session[ 'offset' ] ).
     *
     * @param mixed $offset PHP native session offset.
     *
     * @return bool
     */
    public function has($offset)
    {
        return $this->offsetExists($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::offsetExists
     *
     * Checks if offset exists on PHP native session variable.
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return (bool)isset($_SESSION[ $offset ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__isset
     *
     * Implementing magic method __isset to simplify when checks if offset exists on PHP native session variable,
     * just simply calling isset( $session[ 'offset' ] ).
     *
     * @param mixed $offset PHP native session offset.
     *
     * @return bool
     */
    public function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__get
     *
     * Implementing magic method __get to simplify gets PHP native session variable by requested offset,
     * just simply calling isset( $session[ 'offset' ] ).
     *
     * @param $offset
     *
     * @return mixed
     */
    public function &__get($offset)
    {
        if ($offset === 'id') {
            $_SESSION[ 'id' ] = session_id();
        }

        if ( ! isset($_SESSION[ $offset ])) {
            $_SESSION[ $offset ] = null;
        }

        return $_SESSION[ $offset ];
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__set
     *
     * Implementing magic method __set to simplify set PHP native session variable,
     * just simply calling $session->offset = 'foo'.
     *
     * @param mixed $offset PHP native session offset.
     * @param mixed $value  PHP native session offset value to set.
     */
    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::offsetSet
     *
     * Sets session data into PHP native session global variable.
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $_SESSION[ $offset ] =& $value;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::__unset
     *
     * Implementing magic method __unset to simplify unset method, just simply calling
     * unset( $session[ 'offset' ] ).
     *
     * @param mixed $offset PHP Native session offset
     *
     * @return void
     */
    public function __unset($offset)
    {
        $this->offsetUnset($offset);
    }

    // ------------------------------------------------------------------------

    /**
     * Session::offsetUnset
     *
     * Remove session data from PHP native session global variable.
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($_SESSION[ $offset ])) {
            unset($_SESSION[ $offset ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::getIterator
     *
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new ArrayIterator($_SESSION);
    }

    //--------------------------------------------------------------------

    /**
     * Session::setFlash
     *
     * Sets flash data into the session that will only last for a single request.
     * Perfect for use with single-use status update messages.
     *
     * If $offset is an array, it is interpreted as an associative array of
     * key/value pairs for flash session variables.
     * Otherwise, it is interpreted as the identifier of a specific
     * flash session variable, with $value containing the property value.
     *
     * @param mixed      $offset Flash session variable string offset identifier or associative array of values.
     * @param mixed|null $value  Flash session variable offset value.
     */
    public function setFlash($offset, $value = null)
    {
        $this->set($offset, $value);
        $this->markFlash(is_array($offset) ? array_keys($offset) : $offset);
    }

    //--------------------------------------------------------------------

    /**
     * Session::set
     *
     * Sets session data into PHP native session global variable.
     *
     * If $offset is a string, then it is interpreted as a session property
     * key, and  $value is expected to be non-null.
     *
     * If $offset is an array, it is expected to be an array of key/value pairs
     * to be set as session values.
     *
     * @param string $offset Session offset or associative array of session values
     * @param mixed  $value  Session offset value.
     */
    public function set($offset, $value = null)
    {
        if (is_array($offset)) {
            foreach ($offset as $key => &$value) {
                $_SESSION[ $key ] = $value;
            }

            return;
        }

        $_SESSION[ $offset ] =& $value;
    }

    //--------------------------------------------------------------------

    /**
     * Session::markAsFlash
     *
     * Mark an session offset variables as flash session variables.
     *
     * @param string|array $offset Flash session variable string offset identifier or array of offsets.
     *
     * @return bool Returns FALSE if any flash session variables are not already set.
     */
    public function markFlash($offset)
    {
        if (is_array($offset)) {
            for ($i = 0, $c = count($offset); $i < $c; $i++) {
                if ( ! isset($_SESSION[ $offset[ $i ] ])) {
                    return false;
                }
            }

            $new = array_fill_keys($offset, 'new');

            $_SESSION[ '__o2sessionVariables' ] = isset($_SESSION[ '__o2sessionVariables' ]) ? array_merge(
                $_SESSION[ '__o2sessionVariables' ],
                $new
            ) : $new;

            return true;
        }

        if ( ! isset($_SESSION[ $offset ])) {
            return false;
        }

        $_SESSION[ '__o2sessionVariables' ][ $offset ] = 'new';

        return true;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::get
     *
     * @param string $offset Session offset or associative array of session values
     *
     * @return mixed
     */
    public function get($offset)
    {
        return $this->offsetGet($offset);
    }

    //--------------------------------------------------------------------

    /**
     * Session::offsetGet
     *
     * Gets PHP native session variable value by requested offset.
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ($offset === 'id') {
            $_SESSION[ 'id' ] = session_id();
        }

        return (isset($_SESSION[ $offset ])) ? $_SESSION[ $offset ] : false;
    }

    //--------------------------------------------------------------------

    /**
     * Session::getFlash
     *
     * Retrieve one or more items of flash data from the session.
     * If the offset is null, it will returns all flash session variables.
     *
     * @param string $offset Flash session variable string offset identifier
     *
     * @return array|null    The requested property value, or an assoo2systemative array  of them
     */
    public function getFlash($offset = null)
    {
        if (isset($offset)) {
            return (isset($_SESSION[ '__o2sessionVariables' ], $_SESSION[ '__o2sessionVariables' ][ $offset ], $_SESSION[ $offset ]) &&
                ! is_int($_SESSION[ '__o2sessionVariables' ][ $offset ])) ? $_SESSION[ $offset ] : null;
        }

        $flashVariables = [];

        if ( ! empty($_SESSION[ '__o2sessionVariables' ])) {
            foreach ($_SESSION[ '__o2sessionVariables' ] as $offset => &$value) {
                is_int($value) OR $flashVariables[ $offset ] = $_SESSION[ $offset ];
            }
        }

        return $flashVariables;
    }

    //--------------------------------------------------------------------

    /**
     * Session::keepFlash
     *
     * Keeps a single piece of flash data alive for one more request.
     *
     * @param string|array $offset Flash session variable string offset identifier or array of offsets.
     */
    public function keepFlash($offset)
    {
        $this->markFlash($offset);
    }

    //--------------------------------------------------------------------

    /**
     * Session::unsetFlash
     *
     * Unset flash session variables.
     *
     * @param string $offset Flash session variable string offset identifier or array of offsets.
     */
    public function unsetFlash($offset)
    {
        if (empty($_SESSION[ '__o2sessionVariables' ])) {
            return;
        }

        is_array($offset) OR $offset = [$offset];

        foreach ($offset as $key) {
            if (isset($_SESSION[ '__o2sessionVariables' ][ $key ]) && ! is_int(
                    $_SESSION[ '__o2sessionVariables' ][ $key ]
                )
            ) {
                unset($_SESSION[ '__o2sessionVariables' ][ $key ]);
            }
        }

        if (empty($_SESSION[ '__o2sessionVariables' ])) {
            unset($_SESSION[ '__o2sessionVariables' ]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * Session::getFlashOffsets
     *
     * Gets all flash session variable offsets.
     *
     * @return array Returns array of flash session variable offsets.
     */
    public function getFlashOffsets()
    {
        if ( ! isset($_SESSION[ '__o2sessionVariables' ])) {
            return [];
        }

        $offsets = [];
        foreach (array_keys($_SESSION[ '__o2sessionVariables' ]) as $offset) {
            is_int($_SESSION[ '__o2sessionVariables' ][ $offset ]) OR $offsets[] = $offset;
        }

        return $offsets;
    }

    //--------------------------------------------------------------------

    /**
     * Session::setTemp
     *
     * Sets temporary session variable.
     *
     * @param mixed $offset Temporary session variable string offset identifier or array of offsets.
     * @param null  $value  Temporary session variable offset value.
     * @param int   $ttl    Temporary session variable Time-to-live in seconds
     */
    public function setTemp($offset, $value = null, $ttl = 300)
    {
        $this->set($offset, $value);
        $this->markTemp(is_array($offset) ? array_keys($offset) : $offset, $ttl);
    }

    //--------------------------------------------------------------------

    /**
     * Session::markTemp
     *
     * Mark one of more pieces of data as being temporary, meaning that
     * it has a set lifespan within the session.
     *
     * @param string $offset Temporary session variable string offset identifier.
     * @param int    $ttl    Temporary session variable Time-to-live, in seconds
     *
     * @return bool Returns FALSE if none temporary session variable is set.
     */
    public function markTemp($offset, $ttl = 300)
    {
        $ttl += time();

        if (is_array($offset)) {
            $temp = [];

            foreach ($offset as $key => $value) {
                // Do we have a key => ttl pair, or just a key?
                if (is_int($key)) {
                    $key = $value;
                    $value = $ttl;
                } else {
                    $value += time();
                }

                if ( ! isset($_SESSION[ $key ])) {
                    return false;
                }

                $temp[ $key ] = $value;
            }

            $_SESSION[ '__o2sessionVariables' ] = isset($_SESSION[ '__o2sessionVariables' ]) ? array_merge(
                $_SESSION[ '__o2sessionVariables' ],
                $temp
            ) : $temp;

            return true;
        }

        if ( ! isset($_SESSION[ $offset ])) {
            return false;
        }

        $_SESSION[ '__o2sessionVariables' ][ $offset ] = $ttl;

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Session::getTemp
     *
     * Gets either a single piece or all temporary session variables.
     *
     * @param  string $offset Temporary session variable string offset identifier.
     *
     * @return array Returns temporary session variables.
     */
    public function getTemp($offset = null)
    {
        if (isset($offset)) {
            return (isset($_SESSION[ '__o2sessionVariables' ], $_SESSION[ '__o2sessionVariables' ][ $offset ], $_SESSION[ $offset ]) &&
                is_int($_SESSION[ '__o2sessionVariables' ][ $offset ])) ? $_SESSION[ $offset ] : null;
        }

        $tempVariables = [];

        if ( ! empty($_SESSION[ '__o2sessionVariables' ])) {
            foreach ($_SESSION[ '__o2sessionVariables' ] as $offset => &$value) {
                is_int($value) && $tempVariables[ $offset ] = $_SESSION[ $offset ];
            }
        }

        return $tempVariables;
    }

    // ------------------------------------------------------------------------

    /**
     * Session::unsetTemp
     *
     * Unset temporary session variable.
     *
     * @param mixed $offset Temporary session variable string offset identifier.
     */
    public function unsetTemp($offset)
    {
        if (empty($_SESSION[ '__o2sessionVariables' ])) {
            return;
        }

        is_array($offset) OR $offset = [$offset];

        foreach ($offset as $key) {
            if (isset($_SESSION[ '__o2sessionVariables' ][ $key ]) && is_int(
                    $_SESSION[ '__o2sessionVariables' ][ $key ]
                )
            ) {
                unset($_SESSION[ '__o2sessionVariables' ][ $key ]);
            }
        }

        if (empty($_SESSION[ '__o2sessionVariables' ])) {
            unset($_SESSION[ '__o2sessionVariables' ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Session::getTempOffsets
     *
     * Gets all temporary session variable offsets identifier.
     *
     * @return array Returns array of temporary session variable offsets identifier.
     */
    public function getTempOffsets()
    {
        if ( ! isset($_SESSION[ '__o2sessionVariables' ])) {
            return [];
        }

        $offsets = [];
        foreach (array_keys($_SESSION[ '__o2sessionVariables' ]) as $offset) {
            is_int($_SESSION[ '__o2sessionVariables' ][ $offset ]) && $offsets[] = $offset;
        }

        return $offsets;
    }
}