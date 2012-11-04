<?php

namespace Upload\Handler;

class Xhr extends HandlerAbstract implements HandlerInterface
{
    public function save($path)
    {
        $input = fopen('php://input', 'r');

        if (!$input) {
            throw new \Exception('Stream is not supported');
        }

        $temp_file = tmpfile();
        $stream_size = stream_copy_to_stream($input, $temp_file);

        if ($stream_size != $this->getSize()) {
            throw new \Exception('Stream size and server content length size mismatch');
        }

        $target = fopen($path, 'w');
        fseek($temp_file, 0);
        stream_copy_to_stream($temp_file, $target);
        fclose($target);
        fclose($temp_file);

        return true;
    }

    /**
     * Returns file's name
     * @return string
     * @throws \Exception
     */
    public function getName()
    {
        if (!isset($_GET[$this->id])) {
            throw new \Exception ('File name by identity not exists');
        }

        if (empty($_GET[$this->id])) {
            throw new \Exception ('File name cannot be empty');
        }

        return (string)$_GET[$this->id];
    }

    /**
     * Returns server content length
     * @return int
     * @throws \Exception
     */
    public function getSize()
    {
        if (!isset($_SERVER['CONTENT_LENGTH'])) {
            throw new  \Exception('Server does not support content length');
        }

        return (int)$_SERVER['CONTENT_LENGTH'];
    }
}
