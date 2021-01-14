<?php

namespace Travian\HTMLSax3;

class StateParser
{
    var $htmlsax;
    var $handler_object_element;
    var $handler_method_opening;
    var $handler_method_closing;
    var $handler_object_data;
    var $handler_method_data;
    var $handler_object_pi;
    var $handler_method_pi;
    var $handler_object_jasp;
    var $handler_method_jasp;
    var $handler_object_escape;
    var $handler_method_escape;
    var $handler_default;
    var $parser_options = array();
    var $rawtext;
    var $position;
    var $length;
    var $state = [];

    public function __construct(&$htmlsax)
    {
        $this->htmlsax = &$htmlsax;
        $this->state[XML_HTMLSAX3_STATE_START] = new StartingState();
        $this->state[XML_HTMLSAX3_STATE_CLOSING_TAG] = new ClosingTagState();
        $this->state[XML_HTMLSAX3_STATE_TAG] = new TagState();
        $this->state[XML_HTMLSAX3_STATE_OPENING_TAG] = new OpeningTagState();
        $this->state[XML_HTMLSAX3_STATE_PI] = new PiState();
        $this->state[XML_HTMLSAX3_STATE_JASP] = new JaspState();
        $this->state[XML_HTMLSAX3_STATE_ESCAPE] = new EscapeState();
    }

    public function unscanCharacter()
    {
        $this->position -= 1;
    }

    public function ignoreCharacter()
    {
        $this->position += 1;
    }

    public function scanCharacter()
    {
        if ($this->position < $this->length) {
            return $this->rawtext{$this->position++};
        }
    }

    public function scanUntilString($string)
    {
        $start = $this->position;
        $this->position = strpos($this->rawtext, $string, $start);
        if ($this->position === FALSE) {
            $this->position = $this->length;
        }
        return substr($this->rawtext, $start, $this->position - $start);
    }

    public function scanUntilCharacters($string)
    {
    }

    public function ignoreWhitespace()
    {
    }

    public function parse($data)
    {
        if ($this->parser_options['XML_OPTION_TRIM_DATA_NODES'] == 1) {
            $decorator = new Trim($this->handler_object_data, $this->handler_method_data);
            $this->handler_object_data = $decorator;
            $this->handler_method_data = 'trimData';
        }
        if ($this->parser_options['XML_OPTION_CASE_FOLDING'] == 1) {
            $open_decor = new CaseFolding($this->handler_object_element, $this->handler_method_opening, $this->handler_method_closing);
            $this->handler_object_element = $open_decor;
            $this->handler_method_opening = 'foldOpen';
            $this->handler_method_closing = 'foldClose';
        }
        if ($this->parser_options['XML_OPTION_LINEFEED_BREAK'] == 1) {
            $decorator = new Linefeed($this->handler_object_data, $this->handler_method_data);
            $this->handler_object_data = $decorator;
            $this->handler_method_data = 'breakData';
        }
        if ($this->parser_options['XML_OPTION_TAB_BREAK'] == 1) {
            $decorator = new Tab($this->handler_object_data, $this->handler_method_data);
            $this->handler_object_data = $decorator;
            $this->handler_method_data = 'breakData';
        }
        if ($this->parser_options['XML_OPTION_ENTITIES_UNPARSED'] == 1) {
            $decorator = new Unparsed($this->handler_object_data, $this->handler_method_data);
            $this->handler_object_data = $decorator;
            $this->handler_method_data = 'breakData';
        }
        if ($this->parser_options['XML_OPTION_ENTITIES_PARSED'] == 1) {
            $decorator = new Parsed($this->handler_object_data, $this->handler_method_data);
            $this->handler_object_data = $decorator;
            $this->handler_method_data = 'breakData';
        }
        // Note switched on by default
        if ($this->parser_options['XML_OPTION_STRIP_ESCAPES'] == 1) {
            $decorator = new Stripper($this->handler_object_escape, $this->handler_method_escape);
            $this->handler_object_escape = $decorator;
            $this->handler_method_escape = 'strip';
        }

        $this->rawtext = $data;
        $this->length = strlen($data);
        $this->position = 0;
        $this->parser();
    }

    public function parser($state = XML_HTMLSAX3_STATE_START)
    {
        do {
            $state = $this->state[$state]->parse($this);
        } while ($state != XML_HTMLSAX3_STATE_STOP && $this->position < $this->length);
    }
}