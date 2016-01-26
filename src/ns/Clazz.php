<?php
namespace ns;

class Clazz
{

    private $name;

    private $title;

    public function __construct()
    {
        //
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}