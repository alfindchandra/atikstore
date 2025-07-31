<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActionsController extends Controller
{
    public function index()
    {
        return view('actions.index');
    }
}
