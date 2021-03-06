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

namespace O2System\Kernel\Http\Router\Datastructures;

// ------------------------------------------------------------------------

/**
 * Class Action
 * @package O2System\Kernel\Http\Router\Datastructures
 */
class Action
{
    /**
     * Action Methods
     *
     * @var array
     */
    private $methods;

    /**
     * Action::$domain
     *
     * Routing map domain.
     *
     * @var string
     */
    private $domain;

    /**
     * Action Path
     *
     * @var string
     */
    private $path;

    /**
     * Action Closure
     *
     * @var \Closure
     */
    private $closure;

    /**
     * Action Closure Parameters
     *
     * @var array
     */
    private $closureParameters = [];

    // ------------------------------------------------------------------------

    /**
     * Action::__construct
     *
     * @param string   $method  The route method.
     * @param string   $path    The route path.
     * @param \Closure $closure The route closure.
     * @param string   $domain  The route domain.
     */
    public function __construct($method, $path, \Closure $closure, $domain = null)
    {
        $this->methods = explode('|', $method);
        $this->methods = array_map('strtoupper', $this->methods);

        $this->path = $path;
        $this->closure = $closure;
        $this->domain = is_null($domain)
            ? isset($_SERVER[ 'HTTP_HOST' ])
                ? @$_SERVER[ 'HTTP_HOST' ]
                : @$_SERVER[ 'SERVER_NAME' ]
            : $domain;

        // Remove www
        if (strpos($this->domain, 'www.') !== false) {
            $this->domain = str_replace('www.', '', $this->domain);
        }

        if (preg_match_all("/{(.*)}/", $this->domain, $matches)) {
            foreach ($matches[ 1 ] as $match) {
                $this->closureParameters[] = $match;
            }
        }
    }

    // ------------------------------------------------------------------------

    public function getMethods()
    {
        return $this->methods;
    }

    // ------------------------------------------------------------------------

    public function getDomain()
    {
        return $this->domain;
    }

    // ------------------------------------------------------------------------

    public function getPath()
    {
        return $this->path;
    }

    // ------------------------------------------------------------------------

    public function getClosure()
    {
        return call_user_func_array($this->closure, $this->closureParameters);
    }

    // ------------------------------------------------------------------------

    public function addClosureParameters($value)
    {
        $this->closureParameters[] = $value;

        return $this;
    }

    public function getClosureParameters()
    {
        return $this->closureParameters;
    }

    public function setClosureParameters(array $parameters)
    {
        $this->closureParameters = $parameters;

        return $this;
    }

    // ------------------------------------------------------------------------

    public function isValidDomain()
    {
        $domain = isset($_SERVER[ 'HTTP_HOST' ])
            ? $_SERVER[ 'HTTP_HOST' ]
            : $_SERVER[ 'SERVER_NAME' ];

        if ($this->domain === $domain) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function isValidUriString($uriString)
    {
        $uriString = '/' . ltrim($uriString, '/');

        if (strtolower($uriString) === $this->path) {
            $this->closureParameters = array_merge(
                $this->closureParameters,
                array_filter(explode('/', $uriString))
            );

            return true;
        } elseif (false !== ($matches = $this->getParseUriString($uriString))) {
            $parameters = [];
            $closure = new \ReflectionFunction($this->closure);

            if (is_string(key($matches))) {
                foreach ($closure->getParameters() as $index => $parameter) {
                    if (($class = $parameter->getClass()) instanceof \ReflectionClass) {
                        $className = $class->getName();
                        if (class_exists($className)) {
                            if (isset($matches[ $parameter->name ])) {
                                $parameters[ $index ] = new $className($matches[ $parameter->name ]);
                            }
                        }
                    } elseif (isset($matches[ $parameter->name ])) {
                        $parameters[ $index ] = $matches[ $parameter->name ];
                    } else {
                        $parameters[ $index ] = null;
                    }
                }
            } else {
                foreach ($closure->getParameters() as $index => $parameter) {
                    if (isset($matches[ $index ])) {
                        $parameters[ $index ] = $matches[ $index ];
                    } else {
                        $parameters[ $index ] = null;
                    }
                }
            }

            $this->closureParameters = $parameters;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function getParseUriString($uriString)
    {
        // Convert wildcards to RegEx
        $regex = str_replace(['/(:any?)', ':any', ':num'], ['/?([^/]+)?', '[^/]+', '[0-9]+'], $this->path);
        $regex = str_replace('/', '\/', $regex);

        $uriString = '/' . ltrim($uriString, '/');

        // CodeIgniter Like Routing
        if (preg_match('/' . $regex . '/', $uriString, $matches)) {

            // Remove first match
            array_shift($matches);

            return (count($matches) ? $matches : false);
        }

        // Laravel Like Routing
        if (preg_match("/{(.*)}/", $this->path)) {
            // Try to find from each parts
            $pathParts = explode('/', $this->path);
            $stringParts = explode('/', $uriString);

            $pathParts = array_filter($pathParts);
            $stringParts = array_filter($stringParts);

            $matches = [];
            $parameters = [];

            for ($i = 0; $i <= count($pathParts); $i++) {
                if (isset($pathParts[ $i ]) && isset($stringParts[ $i ])) {
                    if ($pathParts[ $i ] == $stringParts[ $i ]) {
                        $matches[ $i ] = $stringParts[ $i ];
                    }
                }

                if (isset($pathParts[ $i ])) {
                    if (preg_match("/{(.*)}/", $pathParts[ $i ])) {
                        $index = str_replace(['{$', '}'], '', $pathParts[ $i ]);
                        $parameters[ $index ] = isset($stringParts[ $i ]) ? $stringParts[ $i ] : null;
                    }
                }
            }

            return (count($matches) ? $parameters : false);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    public function isValidHttpMethod($method)
    {
        $method = strtoupper($method);

        if (in_array('ANY', $this->methods)) {
            return true;
        }

        return (bool)in_array($method, $this->methods);
    }

    // ------------------------------------------------------------------------

    public function isAnyHttpMethod()
    {
        return (bool)in_array('ANY', $this->methods);
    }
}