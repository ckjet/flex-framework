<?php

namespace Hypilon\Interfaces;

use Hypilon\Http\Request\Request;

interface ControllerInterface
{
    public function beforeAction(Request $request);
    public function afterAction(Request $request);
}