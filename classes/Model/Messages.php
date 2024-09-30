<?php

namespace Oomax\Model;

class Messages
{
    private String $plugin;
    private \stdClass $message;

    public function __construct(String $plugin)
    {
        $this->plugin = $plugin;
    }

    public function generateMessage(Array $message)
    {
        $this->message = new \stdClass();
        foreach ($message as $k => $m)
        {
            $this->message->$k = $m;
        } 
    }

    public function returnMessage(String $name)
    {
        return get_string($name, $this->plugin, $this->message);
    }
}