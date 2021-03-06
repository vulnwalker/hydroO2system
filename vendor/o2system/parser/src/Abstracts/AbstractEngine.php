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

use O2System\Psr\Parser\ParserEngineInterface;
use O2System\Spl\Traits\Collectors\FileExtensionCollectorTrait;
use O2System\Spl\Traits\Collectors\FilePathCollectorTrait;

/**
 * Class AbstractEngine
 *
 * @package O2System\Parser\Abstracts
 */
abstract class AbstractEngine implements ParserEngineInterface
{
    use FileExtensionCollectorTrait;
    use FilePathCollectorTrait;

    public function parsePartials($filename, $vars = null, $optionalFilename = null)
    {
        if (empty($vars)) {
            if (isset($optionalFilename)) {
                return $this->parseFile($optionalFilename);
            }
        } else {
            return $this->parseFile($filename, $vars);
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Base::parseFile
     *
     * @param string $filePath
     * @param array  $vars
     *
     * @return string
     */
    public function parseFile($filePath, array $vars = [])
    {
        if (class_exists('O2System\Framework', false)) {
            return view()->load($filePath, $vars, true);
        } else {
            $fileExtension = '.' . pathinfo($filePath, PATHINFO_EXTENSION);

            if (in_array($fileExtension, $this->fileExtensions) AND is_file($filePath)) {
                return $this->parseString(file_get_contents($filePath), $vars);
            }

            // Try to find from filePaths
            if (count($this->filePaths)) {
                foreach ($this->filePaths as $fileDirectory) {
                    $checkFilePath = $fileDirectory . $filePath;

                    if (in_array($fileExtension, $this->fileExtensions) AND is_file($checkFilePath)) {
                        return $this->parseString(file_get_contents($checkFilePath), $vars);
                        break;
                    }
                }
            }
        }

        return null;
    }
}