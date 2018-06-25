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

namespace O2System\Parser;

// ------------------------------------------------------------------------

use O2System\Psr\Parser\ParserDriverInterface;
use O2System\Psr\Parser\ParserEngineInterface;
use O2System\Psr\Patterns\Structural\Provider\AbstractProvider;
use O2System\Psr\Patterns\Structural\Provider\ValidationInterface;

/**
 * Class Drivers
 *
 * @package O2System\Parser
 */
class Drivers extends AbstractProvider implements ValidationInterface
{
    /**
     * Compiler Config
     *
     * @var Datastructures\Config
     */
    private $config;

    /**
     * Compiler Source File Path
     *
     * @var string
     */
    private $sourceFilePath;

    /**
     * Compiler Source File Directory
     *
     * @var string
     */
    private $sourceFileDirectory;

    /**
     * Compiler Source String
     *
     * @var string
     */
    private $sourceString;

    /**
     * Compiler Vars
     *
     * @var array
     */
    private $vars = [];

    // ------------------------------------------------------------------------

    /**
     * Drivers::__construct
     *
     * @param Datastructures\Config $config
     */
    public function __construct(Datastructures\Config $config)
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('parser');

        $this->config = $config;

        if ($this->config->offsetExists('driver')) {
            $this->loadDriver($this->config->driver, $this->config->getArrayCopy());
        }

        if ($this->config->offsetExists('drivers')) {
            foreach ($this->config->drivers as $driver => $config) {
                if (is_string($driver)) {
                    $this->loadDriver($driver, $config);
                } else {
                    $this->loadDriver($config);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    public function loadDriver($driverOffset, array $config = [])
    {
        $driverClassName = '\O2System\Parser\Drivers\\' . ucfirst($driverOffset) . 'Driver';

        if (class_exists($driverClassName)) {
            if (isset($config[ 'engine' ])) {
                unset($config[ 'engine' ]);
            }

            $this->register((new $driverClassName())->initialize($config), $driverOffset);

            return $this->__isset($driverOffset);
        }

        return false;
    }

    public function addDriver(Abstracts\AbstractDriver $driver, $driverOffset = null)
    {
        $driverOffset = (empty($driverOffset) ? get_class_name($driver) : $driverOffset);
        $driverOffset = strtolower($driverOffset);

        if ($this->config->offsetExists($driverOffset)) {
            $config = $this->config[ $driverOffset ];
        } else {
            $config = $this->config->getArrayCopy();
        }

        if (isset($config[ 'engine' ])) {
            unset($config[ 'engine' ]);
        }

        if ($driver->isInitialize()) {
            $this->register($driver, $driverOffset);
        } else {
            $this->register($driver->initialize($config), $driverOffset);
        }

        return $this->__isset($driverOffset);
    }

    public function getSourceString()
    {
        return $this->sourceString;
    }

    public function loadFile($filePath)
    {
        if ($filePath instanceof \SplFileInfo) {
            $filePath = $filePath->getRealPath();
        }

        if (isset($this->sourceFileDirectory)) {
            if (is_file($this->sourceFileDirectory . $filePath)) {
                $filePath = $this->sourceFileDirectory . $filePath;
            }
        }

        if (is_file($filePath)) {
            $this->sourceFilePath = realpath($filePath);
            $this->sourceFileDirectory = pathinfo($this->sourceFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;

            return $this->loadString(file_get_contents($filePath));
        }

        return false;
    }

    public function loadString($string)
    {
        $this->sourceString = $string;

        if ($this->config->allowPhpScripts === false) {
            $this->sourceString = preg_replace(
                '/<\\?.*(\\?>|$)/Us',
                '',
                str_replace('<?=', '<?php echo ', $this->sourceString)
            );
        }

        $this->sourceString = str_replace(
            [
                '__DIR__',
                '__FILE__',
            ],
            [
                "'" . $this->getSourceFileDirectory() . "'",
                "'" . $this->getSourceFilePath() . "'",
            ],
            $this->sourceString
        );

        return empty($this->sourceString);
    }

    public function getSourceFileDirectory()
    {
        return $this->sourceFileDirectory;
    }

    // ------------------------------------------------------------------------

    public function getSourceFilePath()
    {
        return $this->sourceFilePath;
    }

    public function parse(array $vars = [])
    {
        $output = $this->parsePhp($vars);

        foreach ($this->getIterator() as $driverName => $driverEngine) {
            if ($driverEngine instanceof ParserDriverInterface) {
                if ($driverEngine->isSupported() === false) {
                    continue;
                }

                $engine =& $driverEngine->getEngine();

                if ($engine instanceof ParserEngineInterface) {
                    $engine->addFilePath($this->sourceFileDirectory);
                }

                $driverEngine->loadString($output);
                $output = $driverEngine->parse($this->vars);
            }
        }

        return $output;
    }

    public function parsePhp(array $vars = [])
    {
        $this->loadVars($vars);

        extract($this->vars);

        /*
         * Buffer the output
         *
         * We buffer the output for two reasons:
         * 1. Speed. You get a significant speed boost.
         * 2. So that the final rendered template can be post-processed by
         *  the output class. Why do we need post processing? For one thing,
         *  in order to show the elapsed page load time. Unless we can
         *  intercept the content right before it's sent to the browser and
         *  then stop the timer it won't be accurate.
         */
        ob_start();

        echo eval('?>' . preg_replace('/;*\s*\?>/', '; ?>', $this->sourceString));

        $output = ob_get_contents();
        @ob_end_clean();

        return $output;
    }

    public function loadVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);

        return (bool)empty($this->vars);
    }

    /**
     * Compiler::isValid
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value)
    {
        if ($value instanceof ParserDriverInterface) {
            return true;
        }

        return false;
    }
}