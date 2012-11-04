<?php
namespace Upload\Handler;

abstract class HandlerAbstract
{
    /**
     * @var string to identify file in _GET _FILES array
     */
    protected $id;

    /**
     * @param string $id
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->setFileIdentity($id);
        }
    }

    /**
     * Sets file identity
     * @param $id
     * @return HandlerAbstract
     * @throws \Exception
     */
    public function setFileIdentity($id)
    {
        if (empty($id)) {
            throw new \Exception('Illegal or empty file identity');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Returns identity
     * @return string
     */
    public function getFileIdentity()
    {
        return $this->id;
    }
}
