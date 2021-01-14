<?php

namespace Travian\HTMLSax3;

class Stripper
{
    public $orig_obj;
    public $orig_method;

    public function __construct(&$orig_obj, $orig_method)
    {
        $this->orig_obj =& $orig_obj;
        $this->orig_method = $orig_method;
    }

    public function strip(&$parser, $data)
    {
        if (substr($data, 0, 2) == '--') {
            $patterns = [
                '/^\-\-/', // Opening comment: --
                '/\-\-$/', // Closing comment: --
            ];
            $data = preg_replace($patterns, '', $data);

        } else if (substr($data, 0, 1) == '[') {
            $patterns = [
                '/^\[.*CDATA.*\[/s', // Opening CDATA
                '/\].*\]$/s', // Closing CDATA
            ];
            $data = preg_replace($patterns, '', $data);
        }

        $this->orig_obj->{$this->orig_method}($this, $data);
    }
}