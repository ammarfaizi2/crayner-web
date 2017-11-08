<?php

namespace IceTea\View;

final class View
{
    public static function staticMaker()
    {
        
    }

    public static function buildView($file, $variable)
    {
        return new ViewFoundation($file, $variable);
    }

    public static function make(ViewFoundation $view)
    {
        $st = new CacheHandler(
            $view->getViewFileName(),
            $view->getViewFile()
        );
        if (! $st->isCached()) {
            $st->makeCache();
        }
        unset($content);
        ___viewIsolator(
            $st->getCacheFileName(),
            $view->getVariables()
        );
    }
}
