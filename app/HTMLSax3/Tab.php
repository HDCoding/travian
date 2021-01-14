<?php

namespace Travian\HTMLSax3;

class Tab
{
    public $orig_obj;
    public $orig_method;

    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }

    public function breakData(&$parser, $data)
    {
        $data = explode("\t", $data);
        foreach ($data as $chunk) {
            $this->orig_obj->{$this->orig_method}($this, $chunk);
        }
    }
}