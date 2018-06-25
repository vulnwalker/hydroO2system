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

namespace O2System\Parser\Abstracts;

// ------------------------------------------------------------------------

use O2System\Psr\Parser\ParserDriverInterface;

/**
 * Class AbstractDriver
 *
 * @package O2System\Parser\Abstracts
 */
abstract class AbstractDriver implements ParserDriverInterface
{
    /**
     * Driver Config
     *
     * @var array
     */
    protected $config = [
        'allowPhpScripts'   => true,
        'allowPhpGlobals'   => true,
        'allowPhpFunctions' => true,
        'allowPhpConstants' => true,
    ];

    /**
     * Driver Engine
     *
     * @var object
     */
    protected $engine;

    /**
     * Driver Raw String
     *
     * @var string
     */
    protected $string;

    // ------------------------------------------------------------------------

    /**
     * BaseDriver::loadFile
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function loadFile($filePath)
    {
        if ($filePath instanceof \SplFileInfo) {
            $filePath = $filePath->getRealPath();
        }

        if (is_file($filePath)) {
            return $this->loadString(file_get_contents($filePath));
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * BaseDriver::loadString
     *
     * @param string $string
     *
     * @return bool
     */
    public function loadString($string)
    {
        $this->string = htmlspecialchars_decode($string);

        if ($this->config[ 'allowPhpScripts' ] === false) {
            $this->string = preg_replace(
                '/<\\?.*(\\?>|$)/Us',
                '',
                str_replace('<?=', '<?php echo ', $this->string)
            );
        }

        return (bool)empty($this->string);
    }

    // ------------------------------------------------------------------------

    public function isInitialize()
    {
        return (bool)(empty($this->engine) ? false : true);
    }

    // --------------------------------------------------------------------------------------

    /**
     * BaseDriver::initialize
     *
     * @param array $config
     *
     * @return static
     */
    abstract public function initialize(array $config);

    // ------------------------------------------------------------------------

    public function &getEngine()
    {
        return $this->engine;
    }

    // ------------------------------------------------------------------------

    public function setEngine($engine)
    {
        if ($this->isValidEngine($engine)) {
            $this->engine =& $engine;

            return true;
        }

        return false;
    }

    abstract protected function isValidEngine($engine);

    // ------------------------------------------------------------------------

    public function __call($method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        } elseif (method_exists($this->engine, $method)) {
            return call_user_func_array([&$this->engine, $method], $arguments);
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * BaseDriver::isSupported
     *
     * @return bool
     */
    abstract public function isSupported();
}