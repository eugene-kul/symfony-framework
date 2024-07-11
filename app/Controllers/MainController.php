<?php

namespace App\Controllers;

use App\Models\Fuck;
use Barker\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends BaseController
{
    public function index(Request $request): Response
    {
        return new Response('ку-ку!');
    }
}