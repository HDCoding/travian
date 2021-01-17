<?php

namespace Travian\Libs;

use Travian\HTMLSax3\HTMLSax3;

class SafeHTML
{
    public $_xhtml = '';
    public $_counter = [];
    public $_stack = [];
    public $_dcCounter = [];
    public $_dcStack = [];
    public $_listScope = 0;
    public $_liStack = [];
    public $_protoRegexps = [];
    public $_cssRegexps = [];
    public $singleTags = ['area', 'br', 'img', 'input', 'hr', 'wbr'];
    public $deleteTags = ['applet', 'base', 'basefont', 'bgsound', 'blink', 'body', 'embed', 'frame', 'frameset', 'head', 'html', 'ilayer', 'iframe', 'layer', 'link', 'meta', 'object', 'style', 'title', 'script'];
    public $deleteTagsContent = ['script', 'style', 'title', 'xml'];
    public $protocolFiltering = 'white';
    public $blackProtocols = ['about', 'chrome', 'data', 'disk', 'hcp', 'help', 'javascript', 'livescript', 'lynxcgi', 'lynxexec', 'ms-help', 'ms-its', 'mhtml', 'mocha', 'opera', 'res', 'resource', 'shell', 'vbscript', 'view-source', 'vnd.ms.radio', 'wysiwyg'];
    public $whiteProtocols = ['ed2k', 'file', 'ftp', 'gopher', 'http', 'https', 'irc', 'mailto', 'news', 'nntp', 'telnet', 'webcal', 'xmpp', 'callto',];
    public $protocolAttributes = ['action', 'background', 'codebase', 'dynsrc', 'href', 'lowsrc', 'src'];
    public $cssKeywords = ['absolute', 'behavior', 'behaviour', 'content', 'expression', 'fixed', 'include-source', 'moz-binding'];
    public $noClose = [];
    public $closeParagraph = ['address', 'blockquote', 'center', 'dd', 'dir', 'div', 'dl', 'dt', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'isindex', 'listing', 'marquee', 'menu', 'multicol', 'ol', 'p', 'plaintext', 'pre', 'table', 'ul', 'xmp'];
    public $tableTags = ['caption', 'col', 'colgroup', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr'];
    public $listTags = ['dir', 'menu', 'ol', 'ul', 'dl'];
    public $attributes = ['dynsrc', 'id', 'name'];
    public $attributesNS = ['xml:lang'];

    /**
     * Making regular expressions based on Proto & CSS arrays.
     */
    public function __construct()
    {
        foreach ($this->blackProtocols as $blackProtocol) {
            $preg = "/[\s\x01-\x1F]*";

            for ($i = 0; $i < strlen($blackProtocol); $i++) {
                $preg .= $blackProtocol{$i} . "[\s\x01-\x1F]*";
            }
            $preg .= ":/i";
            $this->_protoRegexps[] = $preg;
        }

        foreach ($this->cssKeywords as $cssKeyword) {
            $this->_cssRegexps[] = '/' . $cssKeyword . '/i';
        }
        return true;
    }

    public function openHandler(&$parser, $name, $attrs)
    {
        $name = strtolower($name);
        if (in_array($name, $this->deleteTagsContent)) {
            array_push($this->_dcStack, $name);
            $this->_dcCounter[$name] = isset($this->_dcCounter[$name]) ? $this->_dcCounter[$name] + 1 : 1;
        }
        if (count($this->_dcStack) != 0) {
            return true;
        }
        if (in_array($name, $this->deleteTags)) {
            return true;
        }
        if (!preg_match("/^[a-z0-9]+$/i", $name)) {
            if (preg_match("!(?:\@|://)!i", $name)) {
                $this->_xhtml .= '&lt;' . $name . '&gt;';
            }
            return true;
        }
        if (in_array($name, $this->singleTags)) {
            $this->_xhtml .= '<' . $name;
            $this->writeAttrs($attrs);
            $this->_xhtml .= ' />';
            return true;
        }
        // TABLES: cannot open table elements when we are not inside table
        if ((isset($this->_counter['table'])) && ($this->_counter['table'] <= 0)
            && (in_array($name, $this->tableTags))) {
            return true;
        }
        // PARAGRAPHS: close paragraph when closeParagraph tags opening
        if ((in_array($name, $this->closeParagraph)) && (in_array('p', $this->_stack))) {
            $this->closeHandler($parser, 'p');
        }

        // LISTS: we should close <li> if <li> of the same level opening
        if ($name == 'li' && count($this->_liStack) &&
            $this->_listScope == $this->_liStack[count($this->_liStack) - 1]) {
            $this->closeHandler($parser, 'li');
        }

        // LISTS: we want to know on what nesting level of lists we are
        if (in_array($name, $this->listTags)) {
            $this->_listScope++;
        }
        if ($name == 'li') {
            array_push($this->_liStack, $this->_listScope);
        }

        $this->_xhtml .= '<' . $name;
        $this->writeAttrs($attrs);
        $this->_xhtml .= '>';
        array_push($this->_stack, $name);
        $this->_counter[$name] = isset($this->_counter[$name]) ? $this->_counter[$name] + 1 : 1;
        return true;
    }

    public function writeAttrs($attrs)
    {
        if (is_array($attrs)) {
            foreach ($attrs as $name => $value) {
                $name = strtolower($name);
                if (strpos($name, 'on') === 0) {
                    continue;
                }
                if (strpos($name, 'data') === 0) {
                    continue;
                }
                if (in_array($name, $this->attributes)) {
                    continue;
                }
                if (!preg_match("/^[a-z0-9]+$/i", $name)) {
                    if (!in_array($name, $this->attributesNS)) {
                        continue;
                    }
                }
                if (($value === TRUE) || (is_null($value))) {
                    $value = $name;
                }
                if ($name == 'style') {
                    // removes insignificant backslahes
                    $value = str_replace("\\", '', $value);
                    // removes CSS comments
                    while (1) {
                        $_value = preg_replace("!/\*.*?\*/!s", '', $value);
                        if ($_value == $value) break;
                        $value = $_value;
                    }
                    // replace all & to &amp;
                    $value = str_replace('&amp;', '&', $value);
                    $value = str_replace('&', '&amp;', $value);

                    foreach ($this->_cssRegexps as $css) {
                        if (preg_match($css, $value)) {
                            continue 2;
                        }
                    }
                    foreach ($this->_protoRegexps as $proto) {
                        if (preg_match($proto, $value)) {
                            continue 2;
                        }
                    }
                }
                $tempval = preg_replace('/&#(\d+);?/me', "chr('\\1')", $value); //"'
                $tempval = preg_replace('/&#x([0-9a-f]+);?/mei', "chr(hexdec('\\1'))", $tempval);
                if ((in_array($name, $this->protocolAttributes)) &&
                    (strpos($tempval, ':') !== false)) {
                    if ($this->protocolFiltering == 'black') {
                        foreach ($this->_protoRegexps as $proto) {
                            if (preg_match($proto, $tempval)) continue 2;
                        }
                    } else {
                        $_tempval = explode(':', $tempval);
                        $proto = $_tempval[0];
                        if (!in_array($proto, $this->whiteProtocols)) {
                            continue;
                        }
                    }
                }

                $value = str_replace("\"", "&quot;", $value);
                $this->_xhtml .= ' ' . $name . '="' . $value . '"';
            }
        }
        return true;
    }

    public function closeHandler(&$parser, $name)
    {
        $name = strtolower($name);
        if (isset($this->_dcCounter[$name]) && ($this->_dcCounter[$name] > 0) &&
            (in_array($name, $this->deleteTagsContent))) {
            while ($name != ($tag = array_pop($this->_dcStack))) {
                $this->_dcCounter[$tag]--;
            }
            $this->_dcCounter[$name]--;
        }
        if (count($this->_dcStack) != 0) {
            return true;
        }
        if ((isset($this->_counter[$name])) && ($this->_counter[$name] > 0)) {
            while ($name != ($tag = array_pop($this->_stack))) {
                $this->closeTag($tag);
            }
            $this->closeTag($name);
        }
        return true;
    }

    public function closeTag($tag)
    {
        if (!in_array($tag, $this->noClose)) {
            $this->_xhtml .= '</' . $tag . '>';
        }
        $this->_counter[$tag]--;
        if (in_array($tag, $this->listTags)) {
            $this->_listScope--;
        }
        if ($tag == 'li') {
            array_pop($this->_liStack);
        }
        return true;
    }

    public function dataHandler(&$parser, $data)
    {
        if (count($this->_dcStack) == 0) {
            $this->_xhtml .= $data;
        }
        return true;
    }

    public function escapeHandler(&$parser, $data)
    {
        return true;
    }

    public function clear()
    {
        $this->_xhtml = '';
        return true;
    }

    public function parse($doc)
    {
        // Save all '<' symbols
        $doc = preg_replace("/<(?=[^a-zA-Z\/\!\?\%])/", '&lt;', $doc);
        // Web documents shouldn't contains \x00 symbol
        $doc = str_replace("\x00", '', $doc);
        // Opera6 bug workaround
        $doc = str_replace("\xC0\xBC", '&lt;', $doc);
        // UTF-7 encoding ASCII decode
        $doc = $this->repackUTF7($doc);
        // Instantiate the parser
        $parser = new HTMLSax3();
        // Set up the parser
        $parser->setObject($this);
        $parser->setElementHandler('openHandler', 'closeHandler');
        $parser->setDataHandler('dataHandler');
        $parser->setEscapeHandler('escapeHandler');
        $parser->parse($doc);
        return $this->getXHTML();
    }

    public function repackUTF7($str)
    {
        return preg_replace_callback('!\+([0-9a-zA-Z/]+)\-!', array($this, 'repackUTF7Callback'), $str);
    }

    public function getXHTML()
    {
        while ($tag = array_pop($this->_stack)) {
            $this->closeTag($tag);
        }
        return $this->_xhtml;
    }

    public function repackUTF7Callback($str)
    {
        $str = base64_decode($str[1]);
        $str = preg_replace_callback('/^((?:\x00.)*)((?:[^\x00].)+)/', array($this, 'repackUTF7Back'), $str);
        return preg_replace('/\x00(.)/', '$1', $str);
    }

    public function repackUTF7Back($str)
    {
        return $str[1] . '+' . rtrim(base64_encode($str[2]), '=') . '-';
    }
}