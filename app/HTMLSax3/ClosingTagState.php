<?php

namespace Travian\HTMLSax3;

class ClosingTagState
{
    public function parse(&$context)
    {
        $tag = $context->scanUntilCharacters('/>');
        if ($tag != '') {
            $char = $context->scanCharacter();
            if ($char == '/') {
                $char = $context->scanCharacter();
                if ($char != '>') {
                    $context->unscanCharacter();
                }
            }
            $context->handler_object_element->{$context->handler_method_closing}($context->htmlsax, $tag, FALSE);
        }
        return XML_HTMLSAX3_STATE_START;
    }
}