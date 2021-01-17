<?php

if (!function_exists('md5_gen')) {
    /**
     * Generates random characters using MD5 values
     * 32 Characters
     */
    function md5_gen(): string
    {
        return md5(uniqid() . time() . microtime());
    }
}

if (!function_exists('make_size')) {
    /**
     * Returns a human readable file size
     *
     * @param integer $bytes
     * Bytes contains the size of the bytes to convert
     *
     * @param integer $decimals
     * Number of decimal places to be returned
     *
     * @return string a string in human readable format
     *
     **/
    function make_size($bytes, $decimals = 2)
    {
        $size = [' B', ' kB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
        $floor = (int)floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $floor)) . $size[$floor];
    }
}

if (!function_exists('sha1_gen')) {
    /**
     * Generates random characters using SHA1 values
     * 40 Characters
     */
    function sha1_gen($token = null): string
    {
        return sha1(uniqid() . time() . microtime() . md5_gen() . $token);
    }
}

if (!function_exists('day_month_year')) {
    /**
     * Return in Brazil format and translated the date: Day Month of Year
     */
    function day_month_year()
    {
        $day_number = date('d');
        $year = date('Y');

        switch (date('w')) {
            case 0:
                $today = 'Domingo';
                break;
            case 1:
                $today = 'Segunda-Feira';
                break;
            case 2:
                $today = 'Terça-Feira';
                break;
            case 3:
                $today = 'Quarta-Feira';
                break;
            case 4:
                $today = 'Quinta-Feira';
                break;
            case 5:
                $today = 'Sexta-Feira';
                break;
            case 6:
                $today = 'Sábado';
                break;
            default:
                $today = 'Bugou';
                break;
        }

        switch (date('n')) {
            case 1:
                $mes = 'Janeiro';
                break;
            case 2:
                $mes = 'Fevereiro';
                break;
            case 3:
                $mes = 'Março';
                break;
            case 4:
                $mes = 'Abril';
                break;
            case 5:
                $mes = 'Maio';
                break;
            case 6:
                $mes = 'Junho';
                break;
            case 7:
                $mes = 'Julho';
                break;
            case 8:
                $mes = 'Agosto';
                break;
            case 9:
                $mes = 'Setembro';
                break;
            case 10:
                $mes = 'Outubro';
                break;
            case 11:
                $mes = 'Novembro';
                break;
            case 12:
                $mes = 'Dezembro';
                break;
            default:
                $mes = 'Bugou';
                break;
        }

        echo "{$today}, {$day_number} de {$mes} de {$year}";
    }
}

if (!function_exists('redirect')) {

    function redirect($url)
    {
        if (!headers_sent()) {
            header("Location: " . $url, true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
            echo '</noscript>';
            exit();
        }
    }
}

function file_fix_write($filename)
{
    if (!is_writable($filename)) {
        if (!@chmod($filename, 0666)) {
            die("Cannot change the mode of file ({$filename})");
        };
    }
}

function get_pop($tid, $level)
{
    $name = "bid" . $tid;
    global $$name;
    $dataarray = $$name;
    $pop = $dataarray[($level + 1)]['pop'];
    $cp = $dataarray[($level + 1)]['cp'];
    return array($pop, $cp);
}

function anti_inject($campo)
{
    foreach ($campo as $key => $val) {
        //remove words that contains syntax sql
        $val = preg_replace("/(ascii|CONCAT|DROP|TABLE_SCHEMA|unhex|group_concat|load_file|information_schma|substring|Union|from|select|insert|delete|where|drop table|show tables|\*|--|\\\\)/", "", $val);
        $val = trim($val); //Remove empty spaces
        $val = strip_tags($val); //Removes tags html/php
        $val = addslashes($val); //Add inverted bars to a string
        $campo[$key] = $val; // store it back into the array
    }
    return $campo; //Returns the the var clean
}

// if inputs still dirty and have dangerous commands THEN convert it to NULL->
function cleaner($value)
{
    $filchars = [
        '.tk', '.info', '.ac', '.net', '.org', '{', '}', '\'', '*', "'", '<', '>', '!', '$', '%', '^', '*'
        , '../', 'column_name', 'order', 'information_schema', 'information_schema.tables', 'table_schema', 'table_name', 'group_concat',
        'database()', 'union', 'x:\#', 'delete ', '///', 'from|xp_|execute|exec|sp_executesql|sp_|select| insert|delete|where|drop table|show tables|#|\*|',
        'DELETE', 'insert', "," | "x'; U\PDATE Character S\ET level=99;-\-", "x';U\PDATE Account S\ET ugradeid=255;-\-",
        "x';U\PDATE Account D\ROP ugradeid=255;-\-", "x';U\PDATE Account D\ROP ", ",W\\HERE 1=1;-\\-",
        "z'; U\PDATE Account S\ET ugradeid=char", 'update', 'drop', 'sele', 'memb', 'set', '$', 'res3t', '%',
        '--', '666.php', '/(shutdown|from|select|update|character|clan|set|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/',
        'c99.php', 'shutdown', 'from', 'select', 'update', 'character', 'UPDATE', 'where', 'show tables', 'alter'
    ];
    return str_replace($filchars, '', $value);
}

function transform_HTML($string, $length = null)
{
    // Helps prevent XSS attacks
    // Remove dead space.
    $string = trim($string);
    // Prevent potential Unicode codec problems.
    $string = utf8_decode($string);
    // HTMLize HTML-specific characters.
    $string = htmlentities($string, ENT_NOQUOTES);
    $string = str_replace('#', '&#35;', $string);
    $string = str_replace('%', '&#37;', $string);
    $length = intval($length);
    if ($length > 0) {
        $string = substr($string, 0, $length);
    }
    return $string;
}

function anti_injection($value)
{
    $value = preg_replace("/(%00|version|users|database|table_schema|into|information_schema|join|create|truncate|convert|char|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss|alert|cmd|passthru|eval|exec|system|fopen|fsockopen|file_get_contents|readfile|unlink|from|select|union|varchar|cast|update|set|insert|delete|where|drop table|show tables|\*|--|\\\\)/", '', $value);
    $value = trim($value);
    $value = strip_tags($value);
    $value = addslashes($value);
    $value = str_replace("'", "''", $value);
    return ($value);
}

function xss_clean($data)
{
    // global $security;
    // If its empty there is no point cleaning it :\
    if (empty($data)) return $data;
    // Recursive loop for arrays
    if (is_array($data)) {
        // foreach($data as $key => $value){ $data[$key] = $this->xss_clean($data); }
        return $data;
    }
    // Fix &entity\n;
    $data = str_replace(array('<?', '?' . '>'), array('&lt;?', '?&gt;'), $data);
    $data = str_replace(array('>', '<'), array('&gt;', '&lt;'), $data);
    // $data = str_replace(array('&','<','>'), array('&','<','>'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    //fix href's
    $data = preg_replace('#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si', '', $data);
    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);
    // $data = $security->xss_clean($data);
    return $data;
}

function safehtml($data)
{
    $safehtml = new \Travian\SafeHTML();
    return $safehtml->parse($data);
}

function filterz($txt)
{
    $arr_simboliu = array('applet', 'alert', 'cookie', 'base64', 'javascript', 'script', "../", "%3c", "%253c", "%3e", "%0e", "%26", "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?", 'Redirect\s+302', 'vbscript\s*:', 'expression\s*(\(|&\#40;)', 'javascript\s*:', 'document.cookie', 'document.write', '.parentNode', '.innerHTML', 'window.location', '-moz-binding', '<!--', '-->', '<![CDATA[', '<comment>', '$', '!', '\'', '%', '^', '<', '>', '{', '}', "'", '\x1a', '\x00', '"', ')', '(');
    $arr_kodu = array('&#35;', '&#36;', '&#33;', '&quot;', '&#37;', '&#94;', '&#63;', '&#95;', '&#45;', '&#43;', '&#124;', '&lt;', '&gt;', '&#123;', '&#125;', '&#91;', '&#93;', '&#44;', '&#039;');
    return str_replace($arr_simboliu, $arr_kodu, $txt);
}

function filterphp($php)
{
    return preg_replace('/<.*?>/', '', $php);
}

function clean($string)
{
    $search = [
        '@<script[^>]*?>.*?</script>@si', // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
    ];
    $string = preg_replace($search, '', $string);
    // $link = mysql_connect('localhost', 't4_farsi', 't4_farsi');
    #Clean inputs for XSS and SQLI
    $string = trim($string);
    $string = htmlspecialchars($string);
    $string = strip_tags($string);
    return $string;
}


