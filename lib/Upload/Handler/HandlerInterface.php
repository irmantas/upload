<?php

namespace Upload\Handler;

interface HandlerInterface
{
    public function getName();
    public function getSize();
    public function setFileIdentity($id);
    public function getFileIdentity();
    public function save($file_path);
}
