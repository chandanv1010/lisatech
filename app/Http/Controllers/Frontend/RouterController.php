<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Core\RouterRepository;

class RouterController extends FrontendController
{
    protected $language;
    protected $routerRepository;
    protected $router;

    public function __construct(
        RouterRepository $routerRepository,
    ) {
        $this->routerRepository = $routerRepository;
        parent::__construct();
    }


    public function index(string $canonical = '', Request $request)
    {
        // 1. Policy 301 Redirect check (-ac{id} -> -a{id})
        if (preg_match('/-ac(\d+)(\.html)?$/i', $canonical, $matches)) {
            $oldId = $matches[1];
            $newCanonicalPattern = preg_replace('/-ac\d+/i', '-a' . $oldId, $canonical);
            $cleanNew = preg_replace('/\.html$/', '', trim($newCanonicalPattern, '/'));
            $targetRouter = \Illuminate\Support\Facades\DB::table('routers')
                ->where('canonical', $cleanNew)
                ->first();
            if ($targetRouter) {
                return redirect()->to(url($targetRouter->canonical . config('apps.general.suffix', '.html')), 301);
            }
        }

        $this->getRouter($canonical);
        if (!is_null($this->router) && !empty($this->router)) {
            $method = 'index';
            $controller = app($this->router->controllers);
            if (method_exists($controller, 'setLanguage')) {
                $controller->setLanguage();
            }
            if (method_exists($controller, 'setSystem')) {
                $controller->setSystem();
            }
            return $controller->{$method}($this->router->module_id, $request);
        } else {
            // Language Fallback for EN or failed requests
            if ($request->has('lang') || session('frontend_locale') === 'en') {
                return redirect()->route('home.index', ['lang' => session('frontend_locale', 'en')]);
            }
            abort(404);
        }
    }

    public function page(string $canonical = '', $page = 1, Request $request)
    {
        $this->getRouter($canonical);
        $request->merge(['page' => $page]);
        $page = (!isset($page)) ? 1 : $page;
        if (!is_null($this->router) && !empty($this->router)) {
            $method = 'index';
            $controller = app($this->router->controllers);
            if (method_exists($controller, 'setLanguage')) {
                $controller->setLanguage();
            }
            if (method_exists($controller, 'setSystem')) {
                $controller->setSystem();
            }
            return $controller->{$method}($this->router->module_id, $request, $page);
        } else {
            if ($request->has('lang') || session('frontend_locale') === 'en') {
                return redirect()->route('home.index', ['lang' => session('frontend_locale', 'en')]);
            }
            abort(404);
        }
    }

    public function getRouter($canonical)
    {
        $cleanCanonical = preg_replace('/\.html$/', '', trim($canonical, '/'));

        // 1. Tra cứu nguyên bản 100% canonical trong bảng routers theo ngôn ngữ hiện tại
        $this->router = $this->routerRepository->findByCondition([
            ['canonical', '=', $cleanCanonical],
            ['language_id', '=', $this->language]
        ]);

        // 2. Nếu không thấy, tra cứu nguyên bản 100% canonical theo ngôn ngữ mặc định (VI)
        if (empty($this->router)) {
            $this->router = $this->routerRepository->findByCondition([
                ['canonical', '=', $cleanCanonical],
                ['language_id', '=', 1]
            ]);
        }
    }
}