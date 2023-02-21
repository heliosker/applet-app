<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;

class UserController extends Controller
{

    public function test()
    {
        return 'hello';
    }

    public function show(Request $request)
    {
        return 'show';
    }

    public function login()
    {
        return 'login';
    }
}
