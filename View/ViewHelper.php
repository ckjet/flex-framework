<?php

namespace Hypilon\View;

class ViewHelper
{
    public function partial($template, $parameters = [])
    {
        $view = new View($template, $parameters);
        return $view->getResponse()->getContent();
    }
}