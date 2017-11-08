<?php

if (! function_exists("env")) {
    function env($key, $def = null)
    {
        return \IceTea\Utils\EnvirontmentVariables::get($key, $def);
    }
}

if (! function_exists("view")) {
    function view($file, $variable = [])
    {
        return \IceTea\View\View::buildView($file, $variable);
    }
}

if (! function_exists("___viewIsolator")) {
    function ___viewIsolator($____file, $____variable = [])
    {
        foreach ($____variable as $____key => $____value) {
            $$____key = $____value;
        }
        return include $____file;
    }
}

if (! function_exists("basepath")) {
    function basepath($file = "")
    {
        return rtrim(realpath(__DIR__."/../../")."/".$file, "/");
    }
}

if (! function_exists("assets")) {
    function assets($assetsFile = "")
    {
        return trim(\IceTea\Utils\Config::get("assets")."/".$assetsFile, "/");
    }
}
