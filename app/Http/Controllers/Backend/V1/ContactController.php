<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\V1\Core\ContactService;
use App\Repositories\Core\ContactRepository;

class ContactController extends Controller
{
    protected $contactService;
    protected $contactRepository;

    public function __construct(
        ContactService $contactService,
        ContactRepository $contactRepository,
    ){
        $this->contactService = $contactService;
        $this->contactRepository = $contactRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'contact.index');
        $contacts = $this->contactService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/contact.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Contact'
        ];
        $config['seo'] = __('messages.contact');
        $template = 'backend.contact.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'contacts'
        ));
    }

    /**
     * Đánh dấu một liên hệ đã xử lý hay chưa.
     *
     * Dùng chung quyền với trang danh sách: ai xem được danh sách thì đánh dấu
     * được, không phát sinh thêm một quyền nữa để rồi không ai gán.
     */
    public function updateStatus(Request $request)
    {
        $this->authorize('modules', 'contact.index');

        $ketQua = $this->contactService->updateHandlingStatus(
            (int) $request->input('id'),
            (int) $request->input('status'),
            Auth::id(),
        );

        if ($ketQua === null) {
            return response()->json([
                'code' => 500,
                'message' => 'Không cập nhật được trạng thái. Hãy thử lại.',
            ], 422);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $ketQua,
        ]);
    }

    public function delete($id){
        $this->authorize('modules', 'contact.destroy');
        $config['seo'] = __('messages.contact');
        $contact = $this->contactRepository->findById($id);
        $template = 'backend.contact.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'contact',
            'config',
        ));
    }

    public function destroy($id){
        if($this->contactService->destroy($id)){
            return redirect()->route('contact.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('contact.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    
    private function config(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
            ]
        ];
    }

}
