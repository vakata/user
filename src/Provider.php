<?php

namespace vakata\user;

class Provider
{
    protected $provider = null;
    protected $id = null;
    protected $data = null;
    protected $name = '';

    public function __construct(string $provider, string $id, $name = '', $data = null)
    {
        $this->provider = $provider;
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
    }

    public function getProvider()
    {
        return $this->provider;
    }
    public function getID()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }
    public function getData()
    {
        return $this->data;
    }
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}