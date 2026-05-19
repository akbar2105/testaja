<?php

if (!function_exists('fmtVal')) {
    function fmtVal($v) {
        $v = (float)$v;
        if ($v == 0) return '0';
        if (floor($v) == $v) return number_format($v, 0, ',', '.');
        return number_format($v, 2, ',', '.');
    }
}

if (!function_exists('fmtIp')) {
    function fmtIp($v) {
        $v = (float)$v;
        if ($v == 0) return '0,00';
        return number_format($v, 2, ',', '.');
    }
}

if (!function_exists('fmtTanam')) {
    function fmtTanam($v) {
        $v = (float)$v;
        if ($v == 0) return '0';
        if (floor($v) == $v) return number_format($v, 0, ',', '.');
        return number_format($v, 2, ',', '.');
    }
}
