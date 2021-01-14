<?php

namespace Travian\HTMLSax3;

class EscapeState
{
    public function parse(&$context)
    {
        $char = $context->ScanCharacter();
        if ($char == '-') {
            $char = $context->ScanCharacter();
            if ($char == '-') {
                $context->unscanCharacter();
                $context->unscanCharacter();
                $text = $context->scanUntilString('-->');
                $text .= $context->scanCharacter();
                $text .= $context->scanCharacter();
            } else {
                $context->unscanCharacter();
                $text = $context->scanUntilString('>');
            }
        } else if ($char == '[') {
            $context->unscanCharacter();
            $text = $context->scanUntilString(']>');
            $text .= $context->scanCharacter();
        } else {
            $context->unscanCharacter();
            $text = $context->scanUntilString('>');
        }

        $context->IgnoreCharacter();
        if ($text != '') {
            $context->handler_object_escape->{$context->handler_method_escape}($context->htmlsax, $text);
        }
        return XML_HTMLSAX3_STATE_START;
    }
}