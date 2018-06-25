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

namespace O2System\Filesystem\Handlers;

// ------------------------------------------------------------------------

use O2System\Kernel\Http\Message\UploadFile;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException;
use O2System\Spl\Iterators\ArrayIterator;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Uploader
 *
 * @package O2System\Filesystem\Handlers
 */
class Uploader
{
    use ErrorCollectorTrait;

    /**
     * Uploader::$path
     *
     * Uploader file destination path.
     *
     * @var string
     */
    protected $path;

    /**
     * Uploader::$maxIncrementFilename
     *
     * Maximum incremental uploaded filename.
     *
     * @var int
     */
    protected $maxIncrementFilename = 100;

    /**
     * Uploader::$allowedMimes
     *
     * Allowed uploaded file mime types.
     *
     * @var array
     */
    protected $allowedMimes;

    /**
     * Uploader::$allowedExtensions
     *
     * Allowed uploaded file extensions.
     *
     * @var array
     */
    protected $allowedExtensions;

    /**
     * Uploader::$allowedFileSize
     *
     * Allowed uploaded file size.
     *
     * @var array
     */
    protected $allowedFileSize = [
        'min' => 0,
        'max' => 0,
    ];

    /**
     * Uploader::$targetFilename
     *
     * Uploader target filename.
     *
     * @var string
     */
    protected $targetFilename;

    protected $uploadedFiles = [];

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::__construct
     *
     * @param array $config
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        language()
            ->addFilePath(str_replace('Handlers', '', __DIR__) . DIRECTORY_SEPARATOR)
            ->loadFile('uploader');

        if ( ! extension_loaded('fileinfo')) {
            throw new BadDependencyCallException('UPLOADER_E_FINFO_EXTENSION');
        }

        if (isset($config[ 'path' ])) {
            $config[ 'path' ] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $config[ 'path' ]);

            if (is_dir($config[ 'path' ])) {
                $this->path = $config[ 'path' ];
            } elseif (defined('PATH_STORAGE')) {
                if (is_dir($config[ 'path' ])) {
                    $this->path = $config[ 'path' ];
                } else {
                    $this->path = PATH_STORAGE . str_replace(PATH_STORAGE, '', $config[ 'path' ]);
                }
            } else {
                $this->path = dirname($_SERVER[ 'SCRIPT_FILENAME' ]) . DIRECTORY_SEPARATOR . $config[ 'path' ];
            }
        } elseif (defined('PATH_STORAGE')) {
            $this->path = PATH_STORAGE;
        } else {
            $this->path = dirname($_SERVER[ 'SCRIPT_FILENAME' ]) . DIRECTORY_SEPARATOR . 'upload';
        }

        $this->path = rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (isset($config[ 'allowedMimes' ])) {
            $this->setAllowedMimes($config[ 'allowedMimes' ]);
        }

        if (isset($config[ 'allowedExtensions' ])) {
            $this->setAllowedExtensions($config[ 'allowedExtensions' ]);
        }

        $this->uploadedFiles = new ArrayIterator();
    }

    /**
     * Uploader::setAllowedMimes
     *
     * Set allowed mime for uploaded file.
     *
     * @param string|array $mimes List of allowed file mime types.
     *
     * @return static
     */
    public function setAllowedMimes($mimes)
    {
        if (is_string($mimes)) {
            $mimes = explode(',', $mimes);
        }

        $this->allowedMimes = array_map('trim', $mimes);

        return $this;
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::setAllowedExtensions
     *
     * Set allowed extensions for uploaded file.
     *
     * @param string|array $extensions List of allowed file extensions.
     *
     * @return static
     */
    public function setAllowedExtensions($extensions)
    {
        if (is_string($extensions)) {
            $extensions = explode(',', $extensions);
        }

        $this->allowedExtensions = array_map('trim', $extensions);

        return $this;
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::setPath
     *
     * Sets uploaded file path.
     *
     * @param string $path [description]
     *
     * @return static
     */
    public function setPath($path = '')
    {
        if (is_dir($path)) {
            $this->path = $path;
        } elseif (defined('PATH_STORAGE')) {
            if (is_dir($path)) {
                $this->path = $path;
            } else {
                $this->path = PATH_STORAGE . str_replace(PATH_STORAGE, '', $path);
            }
        } else {
            $this->path = dirname($_SERVER[ 'SCRIPT_FILENAME' ]) . DIRECTORY_SEPARATOR . $path;
        }
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::setMinFileSize
     *
     * Set minimum file size
     *
     * @param int    $fileSize Allowed minimum file size.
     * @param string $unit     Allowed minimum file size unit conversion.
     *
     * @return static
     */
    public function setMinFileSize($fileSize, $unit = 'M')
    {
        switch ($unit) {
            case 'B':
                $fileSize = (int)$fileSize;
                break;
            case 'K':
                $fileSize = (int)$fileSize * 1000;
                break;
            case 'M':
                $fileSize = (int)$fileSize * 1000000;
                break;
            case 'G':
                $fileSize = (int)$fileSize * 1000000000;
                break;
        }

        $this->allowedFileSize[ 'min' ] = (int)$fileSize;

        return $this;
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::setMaxFileSize
     *
     * Set maximum file size
     *
     * @param int    $fileSize Allowed maximum file size.
     * @param string $unit     Allowed maximum file size unit conversion.
     *
     * @return static
     */
    public function setMaxFileSize($fileSize, $unit = 'M')
    {
        switch ($unit) {
            case 'B':
                $fileSize = (int)$fileSize;
                break;
            case 'K':
                $fileSize = (int)$fileSize * 1000;
                break;
            case 'M':
                $fileSize = (int)$fileSize * 1000000;
                break;
            case 'G':
                $fileSize = (int)$fileSize * 1000000000;
                break;
        }

        $this->allowedFileSize[ 'max' ] = (int)$fileSize;

        return $this;
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::setMaxIncrementFilename
     *
     * @param int $increment Maximum increment counter.
     *
     * @return static
     */
    public function setMaxIncrementFilename($increment = 0)
    {
        $this->maxIncrementFilename = (int)$increment;

        return $this;
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::process
     *
     * @param string|null $field Field offset server uploaded files
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function process($field = null)
    {
        $uploadFiles = input()->files($field);

        if ( ! is_array($uploadFiles)) {
            $uploadFiles = [$uploadFiles];
        }

        if (count($uploadFiles)) {
            foreach ($uploadFiles as $file) {
                if ($file instanceof UploadFile) {
                    if (defined('PATH_STORAGE')) {
                        if ($this->path === PATH_STORAGE) {
                            if (strpos($file->getClientMediaType(), 'image') !== false) {
                                $this->path = $this->path . 'images' . DIRECTORY_SEPARATOR;
                            } else {
                                $this->path = $this->path . 'files' . DIRECTORY_SEPARATOR;
                            }
                        }
                    }

                    $targetPath = $this->path;

                    if (empty($this->targetFilename)) {
                        $this->setTargetFilename($file->getClientFilename());
                    }

                    $filename = $this->targetFilename;

                    if ($this->validate($file)) {
                        if ( ! is_file($filePath = $targetPath . $filename . '.' . $file->getExtension())) {
                            $this->move($file, $filePath);
                        } elseif ( ! is_file($filePath = $targetPath . $filename . '-1' . '.' . $file->getExtension())) {
                            $this->move($file, $filePath);
                        } else {
                            $existingFiles = glob($targetPath . $filename . '*.' . $file->getExtension());
                            if (count($existingFiles)) {
                                $increment = count($existingFiles) - 1;
                            }

                            foreach (range($increment + 1, $increment + 3, 1) as $increment) {
                                if ($increment > $this->maxIncrementFilename) {
                                    $this->errors[] = language()->getLine(
                                        'UPLOADER_E_MAXIMUM_INCREMENT_FILENAME',
                                        [$file->getClientFilename()]
                                    );
                                }

                                if ( ! is_file($filePath = $targetPath . $filename . '-' . $increment . '.' . $file->getExtension())) {
                                    $this->move($file, $filePath);
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if (count($this->errors) == 0) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------------------

    /**
     * Uploader::setTargetFilename
     *
     * Sets target filename.
     *
     * @param string $filename           The target filename.
     * @param string $conversionFunction Conversion function name, by default it's using dash inflector function.
     *
     * @return static
     */
    public function setTargetFilename($filename, $conversionFunction = 'dash')
    {
        $this->targetFilename = call_user_func_array(
            $conversionFunction,
            [
                strtolower(
                    trim(
                        pathinfo($filename, PATHINFO_FILENAME)
                    )
                ),
            ]
        );

        return $this;
    }

    protected function validate(UploadFile $file)
    {
        /* Validate extension */
        if (is_array($this->allowedExtensions) && count($this->allowedExtensions)) {
            if ( ! in_array('.' . $file->getExtension(), $this->allowedExtensions)) {
                $this->errors[] = language()->getLine(
                    'UPLOADER_E_ALLOWED_EXTENSIONS',
                    [implode(',', $this->allowedExtensions), $file->getExtension()]
                );
            }
        }

        /* Validate mime */
        if (is_array($this->allowedMimes) && count($this->allowedExtensions)) {
            if ( ! in_array($file->getFileMime(), $this->allowedMimes)) {
                $this->errors[] = language()->getLine(
                    'UPLOADER_E_ALLOWED_MIMES',
                    [implode(',', $this->allowedMimes), $file->getFileMime()]
                );
            }
        }

        /* Validate min size */
        if ($this->allowedFileSize[ 'min' ] > 0) {
            if ($file->getSize() < $this->allowedFileSize[ 'min' ]) {
                $this->errors[] = language()->getLine(
                    'UPLOADER_E_ALLOWED_MIN_FILESIZE',
                    [$this->allowedFileSize[ 'min' ], $file->getSize()]
                );
            }
        }

        /* Validate max size */
        if ($this->allowedFileSize[ 'min' ] > 0) {
            if ($file->getSize() > $this->allowedFileSize[ 'max' ]) {
                $this->errors[] = language()->getLine(
                    'UPLOADER_E_ALLOWED_MAX_FILESIZE',
                    [$this->allowedFileSize[ 'max' ], $file->getSize()]
                );
            }
        }

        if (count($this->errors) == 0) {
            return true;
        }

        return false;
    }

    protected function move(UploadFile $file, $targetPath)
    {
        $file->moveTo($targetPath);

        if ( ! $file->getError()) {
            $this->uploadedFiles[] = pathinfo($targetPath, PATHINFO_BASENAME);
        } else {
            $this->errors[] = $file->getError();
        }
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }
}