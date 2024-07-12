<?php

namespace App\Http\Controllers;

use App\Business\BusinessFactory;

class MainController extends Controller
{
    protected function factory(): BusinessFactory
    {
        return new BusinessFactory();
    }
}
