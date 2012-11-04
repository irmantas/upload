<?php

namespace Upload;

use Upload\Handler\HandlerInterface;

class Uploader
{
    /**
     * @var array
     */
    protected $_allowExtensions = array();

    /**
     * @var int
     */
    protected $_minSize = 0;

    /**
     * @var int
     */
    protected $_maxSize = 5242880;

    /**
     * @var null
     */
    protected $_uploadDirectory = null;

    /**
     * @var null
     */
    protected $_filename = null;

    /**
     * @var HandlerInterface
     */
    private $_handler;

    const ERR_FILE_EMPTY    = 'err_file_empty';
    const ERR_FILE_LARGE    = 'err_file_large';
    const ERR_FILE_SMALL    = 'err_file_small';
    const ERR_FILE_EXT      = 'err_file_ext';
    const ERR_UPLOAD        = 'err_file_upload';
    const ERR_UPLOAD_DIR    = 'err_file_upload_dir';
    const ERR_WRITE_DIR    = 'err_file_write_dir';

    protected $_errorMessages = array(
        self::ERR_FILE_EMPTY => 'File is empty',
        self::ERR_FILE_LARGE => 'File is too large',
        self::ERR_FILE_SMALL => 'File is too small',
        self::ERR_FILE_EXT => 'File has an invalid extension',
        self::ERR_UPLOAD => 'Upload error',
        self::ERR_UPLOAD_DIR => 'Upload directory is not set',
        self::ERR_WRITE_DIR => 'Upload directory is not writable',
    );

    protected $_errors = array();

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->_handler = $handler;
    }

    /**
     * @param int $size
     * @return Uploader
     */
    public function setMinSize($size = 0)
    {
        $this->_minSize = $this->_checkAndClearSize($size);
        return $this;
    }

    /**
     * @param $size
     * @return Uploader
     */
    public function setMaxSize($size)
    {
        $this->_maxSize = $this->_checkAndClearSize($size);
        return $this;
    }

    /**
     * @param $path
     * @return Uploader
     */
    public function setUploadDir($path)
    {
        $this->_uploadDirectory = rtrim($path, '/') . '/';
        return $this;
    }

    /**
     * @param array $extensions
     * @return Uploader
     */
    public function setAllowExtensions(array $extensions = array())
    {
        $this->_allowExtensions = $extensions;
        return $this;
    }

    /**
     * @param string $filename
     * @return Uploader
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    /**
     * @param $error
     * @return Uploader
     */
    public function setError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Handles upload
     * @return bool
     */
    public function handle()
    {
        if ($this->_check()) {
            $path_info = pathinfo($this->_handler->getName());
            $extension = strtolower($path_info['extension']);

            if (!empty($this->_allowExtensions) && !in_array($extension, $this->_allowExtensions)) {
                $this->setError($this->_errorMessages[self::ERR_FILE_EXT]);
                return false;
            }

            if (!empty($this->_filename)) {
                $filename = $this->_filename;
            } else {
                $filename = $path_info['filename'];
            }

            $save_path = $this->_uploadDirectory . $filename . '.' . $extension;

            if ($this->_handler->save($save_path)) {
                return true;
            }

            $this->setError($this->_errorMessages[self::ERR_UPLOAD]);
        }

        return false;
    }

    /**
     * Checks for config
     * @return bool
     */
    private function _check()
    {
        if (empty($this->_uploadDirectory)) {
            $this->setError($this->_errorMessages[self::ERR_UPLOAD_DIR]);
        }

        if (!is_writable($this->_uploadDirectory)) {
            $this->setError($this->_errorMessages[self::ERR_WRITE_DIR]);
        }

        $size = $this->_handler->getSize();
        if ($size == 0) {
            $this->setError($this->_errorMessages[self::ERR_FILE_EMPTY]);
        }

        if ($size > $this->_maxSize) {
            $this->setError($this->_errorMessages[self::ERR_FILE_LARGE]);
        }

        if ($size < $this->_minSize) {
            $this->setError($this->_errorMessages[self::ERR_FILE_SMALL]);
        }

        return !$this->hasErrors();
    }

    /**
     * @param array $messages
     * @throws \Exception
     */
    public function setErrorMessages(array $messages = array())
    {
        if (!empty($messages)) {
            foreach ($messages as $type => $message) {
                if (!isset($this->_errorMessages[$type])) {
                    throw new \Exception('Unknown error message');
                }

                $this->_errorMessages[$type] = $message;
            }
        }
    }

    /**
     * @param $size
     * @return int
     * @throws \Exception
     */
    private function _checkAndClearSize($size)
    {
        $size = (int)$size;
        if ($size < 0) {
            throw new \Exception('Upload size cannot be negative value');
        }

        return $size;
    }
}
