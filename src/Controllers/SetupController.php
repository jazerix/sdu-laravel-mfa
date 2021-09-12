<?php

namespace SDU\MFA\Controllers;

use Illuminate\Routing\Controller;

class SetupController extends Controller
{
    public function showSetup()
    {
        return view('SDU\MFA::setup');
    }
}