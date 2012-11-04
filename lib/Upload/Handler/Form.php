<?php

namespace Upload\Handler;

class Form extends HandlerAbstract implements HandlerInterface
{
    /**
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->_checkFileIdentity();
    }

    /**
     * Moves uploaded file
     * @param $path
     * @return bool
     */
    public function save($path)
    {
        return move_uploaded_file($_FILES[$this->id]['tmp_name'], $path);
    }

    /**
     * Returns file size
     * @return int
     */
    public function getSize()
    {
        return (int)$_FILES[$this->id]['size'];
    }

    /**
     * Returns original file name
     * @return string
     */
    public function getName()
    {
        return (string)$_FILES[$this->id]['name'];
    }

    /**
     * Checks if uploaded element exists
     * @throws \Exception
     */
    protected function _checkFileIdentity()
    {
        if (!isset($_FILES[$this->id]) || empty($_FILES[$this->id])) {
            throw new \Exception('Cannot find file by given identity');
        }
    }
}
