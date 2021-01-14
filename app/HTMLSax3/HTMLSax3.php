<?php

namespace Travian\HTMLSax3;

class HTMLSax3
{
    public $state_parser;

    public function __construct()
    {
        if (version_compare(phpversion(), '4.3', 'ge')) {
            $this->state_parser = new StateParserGtet430($this);
        } else {
            $this->state_parser = new StateParserLt430($this);
        }

        $nullhandler = new NullHandler();

        $this->setObject($nullhandler);
        $this->setElementHandler('DoNothing', 'DoNothing');
        $this->setDataHandler('DoNothing');
        $this->setPiHandler('DoNothing');
        $this->setJaspHandler('DoNothing');
        $this->setEscapeHandler('DoNothing');
    }

    public function setObject(&$object)
    {
        if (is_object($object)) {
            $this->state_parser->handler_default = $object;
            return true;
        } else {
            return 'HTMLSax3::setObject requires an object instance';
        }
    }

    public function setElementHandler($opening_method, $closing_method)
    {
        $this->state_parser->handler_object_element = $this->state_parser->handler_default;
        $this->state_parser->handler_method_opening = $opening_method;
        $this->state_parser->handler_method_closing = $closing_method;
    }

    public function setDataHandler($data_method)
    {
        $this->state_parser->handler_object_data = $this->state_parser->handler_default;
        $this->state_parser->handler_method_data = $data_method;
    }

    public function setPiHandler($pi_method)
    {
        $this->state_parser->handler_object_pi = $this->state_parser->handler_default;
        $this->state_parser->handler_method_pi = $pi_method;
    }

    public function setJaspHandler($jasp_method)
    {
        $this->state_parser->handler_object_jasp = $this->state_parser->handler_default;
        $this->state_parser->handler_method_jasp = $jasp_method;
    }

    public function setEscapeHandler($escape_method)
    {
        $this->state_parser->handler_object_escape = $this->state_parser->handler_default;
        $this->state_parser->handler_method_escape = $escape_method;
    }

    public function setOption($name, $value = 1)
    {
        if (array_key_exists($name, $this->state_parser->parser_options)) {
            $this->state_parser->parser_options[$name] = $value;
            return true;
        } else {
            return "HTMLSax3::setOption({$name}) illegal";
        }
    }

    public function getCurrentPosition()
    {
        return $this->state_parser->position;
    }

    public function getLength()
    {
        return $this->state_parser->length;
    }
}