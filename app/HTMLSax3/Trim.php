<?php

namespace Travian\HTMLSax3;

class Trim
{
    public $orig_obj;
    public $orig_method;

    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }

    public function trimData(&$parser, $data)
    {
        $data = trim($data);
        if ($data != '') {
            $this->orig_obj->{$this->orig_method}($parser, $data);
        }
    }
}