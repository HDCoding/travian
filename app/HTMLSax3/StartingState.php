<?php

namespace Travian\HTMLSax3;

class StartingState
{
    public function parse(&$context)
    {
        $data = $context->scanUntilString('<');
        if ($data != '') {
            $context->handler_object_data->{$context->handler_method_data}($context->htmlsax, $data);
        }
        $context->IgnoreCharacter();
        return XML_HTMLSAX3_STATE_TAG;
    }
}