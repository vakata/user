<?php

namespace vakata\user;

class Provider
{
    protected string $provider;
    protected string $id;
    protected string $name;
    protected string $data;
    protected int $created;
    protected ?int $used = null;
    protected bool $disabled = false;

    public function __construct(
        string $provider,
        string $id,
        string $name = '',
        string $data = null,
        string $created = null,
        string $used = null,
        bool $disabled = false
    )
    {
        $this->provider = $provider;
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
        $this->created = $created ? strtotime($created) : time();
        $this->used = $used ? strtotime($used) : null;
        $this->disabled = $disabled;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
    public function getID(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getData(): string
    {
        return $this->data;
    }
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }
    public function getCreated(): int
    {
        return $this->created;
    }
    public function getUsed(): ?int
    {
        return $this->used;
    }
    public function setCreated(string $created): self
    {
        $this->created = strtotime($created);
        return $this;
    }
    public function setUsed(string $used): self
    {
        $this->used = $used ? strtotime($used) : null;
        return $this;
    }
    public function enabled() : bool
    {
        return $this->disabled === false;
    }
    public function disabled(): bool
    {
        return $this->disabled === true;
    }
    public function enable(): void
    {
        $this->disabled = false;
    }
    public function disable(): void
    {
        $this->disabled = true;
    }
}