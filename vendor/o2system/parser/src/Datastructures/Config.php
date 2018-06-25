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

namespace O2System\Parser\Datastructures;

// ------------------------------------------------------------------------

/**
 * Class Config
 *
 * @package O2System\Parser\Metadata
 */
class Config extends \O2System\Kernel\Datastructures\Config
{
    /**
     * Config::__construct
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $defaultConfig = [
            'driver'            => 'moustache',
            'allowPhpScripts'   => true,
            'allowPhpFunctions' => true,
            'allowPhpConstants' => true,
            'allowPhpGlobals'   => true,
        ];

        $config = array_merge($defaultConfig, $config);

        parent::__construct($config);
    }
}