<?php

namespace Travian\HTMLSax3;

class CaseFolding
{
    public $orig_obj;
    public $orig_open_method;
    public $orig_close_method;

    public function __construct(&$orig_obj, $orig_open_method, $orig_close_method)
    {
        $this->orig_obj =& $orig_obj;
        $this->orig_open_method = $orig_open_method;
        $this->orig_close_method = $orig_close_method;
    }

    public function foldOpen(&$parser, $tag, $attrs = array(), $empty = FALSE)
    {
        $this->orig_obj->{$this->orig_open_method}($parser, strtoupper($tag), $attrs, $empty);
    }

    public function foldClose(&$parser, $tag, $empty = FALSE)
    {
        $this->orig_obj->{$this->orig_close_method}($parser, strtoupper($tag), $empty);
    }
}