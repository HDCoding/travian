<?php

namespace Travian\HTMLSax3;

class TagState
{
    public function parse(&$context)
    {
        switch ($context->ScanCharacter()) {
            case '/':
                return XML_HTMLSAX3_STATE_CLOSING_TAG;
                break;
            case '?':
                return XML_HTMLSAX3_STATE_PI;
                break;
            case '%':
                return XML_HTMLSAX3_STATE_JASP;
                break;
            case '!':
                return XML_HTMLSAX3_STATE_ESCAPE;
                break;
            default:
                $context->unscanCharacter();
                return XML_HTMLSAX3_STATE_OPENING_TAG;
        }
    }
}