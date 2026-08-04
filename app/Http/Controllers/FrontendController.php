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

    /**
     * Site settings, memoised per language for the lifetime of the request.
     *
     * RouterController resolves the target controller with app() and then calls
     * setSystem() on it by hand, because controller middleware does not run for a
     * manually resolved instance. Its own middleware has already run setSystem()
     * on itself by then, so without the cache every frontend page read the whole
     * systems table twice.
     */
    public function setSystem(){
        $key = 'frontend.system.'.$this->language;

        // scoped(), not a static array: a static would live for the whole PHP
        // process, so a long-running worker would keep serving settings from before
        // an admin edited them. A scoped binding is resolved once per request and
        // dropped when the container is rebuilt.
        if (!app()->bound($key)) {
            app()->scoped($key, function () {
                return convert_array(
                    System::where('language_id', $this->language)->get(), 'keyword', 'content'
                );
            });
        }

        $this->system = app($key);
    }
   

}
