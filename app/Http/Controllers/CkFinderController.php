<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Session;
use Validator;

class CkFinderController extends Controller
{
    public function browser()
    {
        return view('admin.ckfinder');
    }
}
