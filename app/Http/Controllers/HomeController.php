<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function contacto()
    {
        return view('info.contacto');
    }

    public function dondeEstamos()
    {
        return view('info.donde_estamos');
    }

    public function acercaDe()
    {
        return view('info.acerca_de');
    }

    public function avisoLegal()
    {
        return view('info.aviso_legal');
    }

    public function politicaPrivacidad()
    {
        return view('info.politica_privacidad');
    }

    public function terminosCondiciones()
    {
        return view('info.terminos_condiciones');
    }
}
