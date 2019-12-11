<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Events\Test;

class PagesController extends Controller
{
    public function root()
    {
        return view('pages.root');
    }
}
