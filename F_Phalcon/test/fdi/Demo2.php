<?php

class Demo2
{
    public $number = 1;

    public function __construct()
    {
        echo "Demo2::__construct()\n";   
    }

    public function onConstruct()
    {
        echo "Demo2::onConstruct()\n";
        $this->number = 2;
    }

    public function onInitialize()
    {
        echo "Demo2::onInitialize()\n";
        $this->number = 3;
    }

    public function onInit()
    {
        $this->onInitialize();
    }
}
