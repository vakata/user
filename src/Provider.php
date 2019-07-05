<?php

namespace vakata\user;

class Provider
{
    protected $provider = null;
    protected $id = null;
    protected $data = null;
    protected $name = '';
    protected $created = null;
    protected $used = null;

    public function __construct(string $provider, string $id, $name = '', $data = null, $created = null, $used = null)
    {
        $this->provider = $provider;
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
        $this->created = $created ? strtotime($created) : time();
        $this->used = $used ? strtotime($used) : null;
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
    public function getCreated()
    {
        return $this->created;
    }
    public function getUsed()
    {
        return $this->used;
    }
    public function setCreated($created)
    {
        $this->created = strtotime($created);
        return $this;
    }
    public function setUsed($used)
    {
        $this->used = $used ? strtotime($used) : null;
        return $this;
    }
}