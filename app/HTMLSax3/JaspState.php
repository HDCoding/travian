<?php

namespace Travian\HTMLSax3;

class JaspState
{
    public function parse(&$context)
    {
        $text = $context->scanUntilString('%>');
        if ($text != '') {
            $context->handler_object_jasp->{$context->handler_method_jasp}($context->htmlsax, $text);
        }
        $context->IgnoreCharacter();
        $context->IgnoreCharacter();
        return XML_HTMLSAX3_STATE_START;
    }
}