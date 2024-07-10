<?php

namespace App\Controllers;

use App\Models\Fuck;
use Barker\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FuckController extends BaseController
{
    public function index(Request $request, int $year): Response
    {
        $leapYear = new Fuck();

        $text = 'Nope, this is not a leap year. - ' . rand();

        if ($leapYear->isLeapYear($year)) {
            $text = 'Yep, this is a leap year! - ' . rand();
        }

        $response = new Response($text);
        $response->headers->set('Skip-Google', 'yes');
        $response->setTtl(10);

        return $response;
    }
}