<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Language;
use App\Models\System;

class FrontendController extends Controller
{
    protected $language;
    protected $systemRepository;
    protected $system;

    public function __construct(
        // SystemRepository $systemRepository
    ){
        $this->middleware(function ($request, $next) {
            $this->setLanguage();
            $this->setSystem();
            return $next($request);
        });
    }

    public function setLanguage(){
        $this->language = config('app.language_id', 1);
    }

    public function setSystem(){
        $this->system = convert_array(System::where('language_id', $this->language)->get(), 'keyword', 'content');
    }
   

}
