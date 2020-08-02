<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function store($id)
    {
        \Auth::user()->like($id);
        return back();
    }

    public function destroy($id)
    {
        \Auth::user()->like($id);
        return back();
    }
}
