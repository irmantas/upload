<?php

namespace Upload\Handler;

class Form extends HandlerAbstract implements HandlerInterface
{
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->_checkFileIdentity();
    }

    public function save($path)
    {
        return move_uploaded_file($_FILES[$this->id]['tmp_name'], $path);
    }

    public function getSize()
    {
        return $_FILES[$this->id]['name'];
    }

    public function getName()
    {
        return $_FILES[$this->id]['size'];
    }

    protected function _checkFileIdentity()
    {
        if (!isset($_FILES[$this->id]) || empty($_FILES[$this->id])) {
            throw new \Exception('Cannot find file by given identity');
        }
    }
}
