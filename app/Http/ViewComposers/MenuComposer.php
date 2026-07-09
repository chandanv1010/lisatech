<?php  
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Menu\MenuCatalogueRepository;

class MenuComposer
{

    protected $menuCatalogueRepository;
    protected $language;
    
    protected static $menuData = [];

    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        $language
    ){
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {

        $dataKey = 'menu_lang_'.$this->language;
        if(!isset(static::$menuData[$dataKey])){
            static::$menuData[$dataKey] = $this->loadMenuData();
        }
        $view->with('menu', static::$menuData[$dataKey]);

    }

    private function loadMenuData(){
        
        $agrument = $this->agrument($this->language);
        $menuCatalogue = $this->menuCatalogueRepository->findByCondition(...$agrument);
        $menus = [];
        $htmlType = ['main-menu', 'main'];
         if(count($menuCatalogue)){
            foreach($menuCatalogue as $key => $val){
                $type = (in_array($val->keyword, $htmlType)) ? 'html' : 'array';
                $recursiveMenus = recursive($val->menus);
                
                if (in_array($val->keyword, $htmlType)) {
                    $canonicalsToLookup = [];
                    foreach ($recursiveMenus as $k1 => $item1) {
                        if (!empty($item1['children'])) {
                            foreach ($item1['children'] as $k2 => $item2) {
                                if (empty($item2['children'])) {
                                    $menuObj = $item2['item'];
                                    $translation = $menuObj->languages->firstWhere('id', $this->language) ?? $menuObj->languages->first();
                                    $pivot = $translation?->pivot;
                                    if ($pivot && !empty($pivot->canonical)) {
                                        $raw = $pivot->canonical;
                                        $clean = preg_replace('/\.html$/', '', trim($raw, '/'));
                                        $canonicalsToLookup[$raw] = $raw;
                                        $canonicalsToLookup[$clean] = $clean;
                                    }
                                }
                            }
                        }
                    }

                    $routers = collect();
                    if (!empty($canonicalsToLookup)) {
                        $routers = \Illuminate\Support\Facades\DB::table('routers')
                            ->whereIn('canonical', array_values($canonicalsToLookup))
                            ->where('controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController')
                            ->get()
                            ->keyBy('canonical');
                    }

                    $pcIds = [];
                    $menuToPcIdMap = [];
                    foreach ($recursiveMenus as $k1 => $item1) {
                        if (!empty($item1['children'])) {
                            foreach ($item1['children'] as $k2 => $item2) {
                                if (empty($item2['children'])) {
                                    $menuObj = $item2['item'];
                                    $translation = $menuObj->languages->firstWhere('id', $this->language) ?? $menuObj->languages->first();
                                    $pivot = $translation?->pivot;
                                    if ($pivot && !empty($pivot->canonical)) {
                                        $raw = $pivot->canonical;
                                        $clean = preg_replace('/\.html$/', '', trim($raw, '/'));
                                        $router = $routers->get($clean) ?? $routers->get($raw);
                                        if ($router) {
                                            $pcId = $router->module_id;
                                            $pcIds[$pcId] = $pcId;
                                            $menuToPcIdMap[$menuObj->id] = $pcId;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $childCatsGrouped = [];
                    if (!empty($pcIds)) {
                        $childCats = \App\Models\ProductCatalogue::whereIn('parent_id', array_keys($pcIds))
                            ->where('publish', 2)
                            ->whereNull('deleted_at')
                            ->orderBy('order', 'asc')
                            ->orderBy('id', 'desc')
                            ->with(['languages' => fn($q) => $q->where('language_id', $this->language)])
                            ->get();
                        foreach ($childCats as $childCat) {
                            $childCatsGrouped[$childCat->parent_id][] = $childCat;
                        }
                    }

                    foreach ($recursiveMenus as $k1 => $item1) {
                        if (!empty($item1['children'])) {
                            foreach ($item1['children'] as $k2 => $item2) {
                                if (empty($item2['children'])) {
                                    $menuObj = $item2['item'];
                                    $pcId = $menuToPcIdMap[$menuObj->id] ?? null;
                                    if ($pcId && isset($childCatsGrouped[$pcId])) {
                                        $virtualChildren = [];
                                        foreach ($childCatsGrouped[$pcId] as $childCat) {
                                            $childLang = $childCat->languages->first();
                                            if ($childLang) {
                                                $mockMenu = new \App\Models\Menu();
                                                $mockMenu->id = 2000000 + $childCat->id;
                                                $mockMenu->parent_id = $menuObj->id;
                                                
                                                $mockPivot = new \stdClass();
                                                $mockPivot->name = $childLang->pivot->name ?? $childLang->name;
                                                $mockPivot->canonical = $childLang->pivot->canonical ?? $childLang->canonical;
                                                
                                                $mockLanguage = new \App\Models\Language();
                                                $mockLanguage->id = $this->language;
                                                $mockLanguage->setRelation('pivot', $mockPivot);
                                                
                                                $mockMenu->setRelation('languages', collect([$mockLanguage]));
                                                
                                                $virtualChildren[] = [
                                                    'item' => $mockMenu,
                                                    'children' => []
                                                ];
                                            }
                                        }
                                        $recursiveMenus[$k1]['children'][$k2]['children'] = $virtualChildren;
                                    }
                                }
                            }
                        }
                    }
                }
                
                if($type == 'html'){
                    $menus['mobile'] = $recursiveMenus;
                    
                    $menus[$val->keyword] = frontend_recursive_menu($recursiveMenus, 0, 3, 'html');
                    
                    $menus[$val->keyword . '_array'] = frontend_recursive_menu($recursiveMenus, 0, 3, 'array');
                } else {
                    $menus[$val->keyword] = frontend_recursive_menu($recursiveMenus, 0, 3, 'array');
                }
            }
        }
        return $menus;
    }


    private function agrument($language){

        
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [
                'menus' => function($query) use ($language) {
                    $query->orderBy('order', 'desc');
                    $query->with([
                        'languages' => function($query) use ($language){
                            $query->whereIn('language_id', [$language, 1]);
                        }
                    ]);
                }
            ]
        ];
    }
}