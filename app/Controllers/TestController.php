<?php

namespace App\Controllers;

use App\Models\Fuck;
use Barker\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends BaseController
{
    public function index(Request $request, int $year): Response
    {
        $response = new Response("
            This is the content of your page. <br>
            <br> Is $year a leap year? <esi:include src=\"/is_leap_year/$year\"/> <br>
            Some other content
        ");

        $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');

        return $response;
    }
}