<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


    function dump($value){

        echo "<pre>";
        var_dump($value);
        echo "</pre>";

    }

    function die_dump($value){

        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        die();

    }

    function pr($value)
    {
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    }

    function prd($value)
    {
        pr($value);
        die();
    }

    function log_write($data, $new = false) {
        $data = print_r($data, true);
        $mode = $new ? 'w' : 'w+';
        $fh = fopen('/tmp/indow_log', $mode);
        fwrite($fh, $data);
        fclose($fh);
    }


    function clean_string($string)
    {
        $symbols = array('/', '\\', '\'', '"', ',', '.', '<', '>', '?', ';', ':', '[', ']', '{', '}', '|', '=', '+', '_', ')', '(', '*', '&', '^', '%', '$', '#', '@', '!', '~', '`');
        for ($i = 0; $i < sizeof($symbols); $i++) {
            $string = str_replace($symbols[$i], '', $string);
        }
        return strtolower(str_replace(' ', '', trim($string)));
    }