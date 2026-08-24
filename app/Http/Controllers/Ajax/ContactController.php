<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\V1\Core\ContactService;
use Illuminate\Support\Facades\DB;
use App\Models\Scholar;

class ContactController extends Controller
{

    protected $contactService;
    
    public function __construct(
        ContactService $contactService
    ){
        $this->contactService = $contactService;
    }

    public function requestConsult(Request $request){
        $flag = $this->contactService->create($request);
        return response()->json([
            'status' => $flag['code'] == 10 ? true : false,
            'messages' => 'Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ với bạn',
        ]);
    }

    public function quickConsult(Request $request){
        $flag = $this->contactService->create($request);
        return response()->json([
            'status' => $flag['code'] == 10 ? true : false,
            'messages' => 'Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ với bạn',
        ]);
    }

    public function advise(Request $request){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];
        
        $errorMessages = [
            'name.required' => 'Bạn chưa nhập họ tên.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
        ];

        $validator = Validator::make($request->all(), $rules, $errorMessages);

        if($validator->fails()) {
            $errors = $validator->errors();
            $response = [
                'status' => 422,
                'messages' => [
                    'name' => $errors->first('name'),
                    'phone' => $errors->first('phone'),
                ],
            ];
        
            return response()->json($response);
        }

        $flag = $this->contactService->create($request);

        return response()->json([
            'response' => $flag, 
            'messages' => 'Đặt hàng thành công',
            'code' => (!$flag) ? 11 : 10,
        ]);  
    }

    public function create(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'scholarshipType' => 'nullable|integer|min:1',
            'address' => 'nullable|string|max:500',
            'message' => 'nullable|string',
        ]);

        $scholarName = null;
        if (!empty($validated['scholarshipType'])) {
            $scholar = Scholar::with(['languages'])->find($validated['scholarshipType']);
            if ($scholar && $scholar->languages->first()) {
                $scholarName = $scholar->languages->first()->pivot->name;
            }
        }

        try {
            DB::beginTransaction();
            $msg = $validated['message'] ?? '';
            if ($scholarName) {
                $msg .= '<div>Loại học bổng/chương trình: ' . $scholarName . '</div>';
            }

            $id = DB::table('contacts')->insertGetId([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? '',
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? '',
                'message' => $msg,
                'created_at' => now(),
                'updated_at' => now() 
            ]);
            DB::commit();

            ContactService::sendNotificationMail([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? '',
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? '',
                'message' => $msg,
                'type_name' => 'Đăng ký nhận thông tin liên hệ / Khóa học',
                'created_at' => now(),
            ]);

            return response()->json([
                'message' => 'Xử lý yêu cầu thành công',
                'code' => '200',
                'flag' => true
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Ajax ContactController create error: ' . $th->getMessage());
            return response()->json([
                'message' => 'Có vấn đề xảy ra trong quá trình xử lý: ' . $th->getMessage(),
                'code' => '500',
                'flag' => false
            ]);
        }
    }

     public function createScholar(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'destination_area' => 'nullable',
            'apply_for' => 'nullable',
            'address' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $msg = '<div>
                <h1>Đăng ký nhận tư vấn học bổng</h1>
                <div>Loại học bổng: '.($validated['apply_for'] ?? '').'</div>
                <div>Khu vực: '.($validated['destination_area'] ?? '').'</div>
            </div>';

            DB::table('contacts')->insert([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? '',
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? '',
                'message' => $msg,
                'created_at' => now(),
                'updated_at' => now() 
            ]);
            DB::commit();

            ContactService::sendNotificationMail([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? '',
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? '',
                'message' => $msg,
                'type_name' => 'Đăng ký tư vấn học bổng',
                'created_at' => now(),
            ]);

            return response()->json([
                'message' => 'Xử lý yêu cầu thành công',
                'code' => '200',
                'flag' => true
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Ajax createScholar error: ' . $th->getMessage());
            return response()->json([
                'message' => 'Có vấn đề xảy ra trong quá trình xử lý',
                'code' => '500',
                'flag' => false
            ]);
        }
    }

    public function buyNow(Request $request){
         $validated = $request->validate([
                'order_name'        => 'required|string|max:255',
                'order_email'       => 'nullable|email',
                'order_phone'       => 'required|string',
                'order_address'     => 'nullable|string|max:255',
                'order_title_prd'   => 'required|string|max:255',
                'order_message'     => 'nullable|string',
            ]);
        try {
            DB::beginTransaction();

            $msg = '
                <div>
                    <h1>Đặt mua / Tư vấn sản phẩm</h1>
                    <div>Sản phẩm: ' . $validated['order_title_prd'] . '</div>
                    <div>Lời nhắn: ' . ($validated['order_message'] ?? '') . '</div>
                </div>';

            DB::table('contacts')->insert([
                'name'       => $validated['order_name'],
                'email'      => $validated['order_email'] ?? '',
                'phone'      => $validated['order_phone'],
                'address'    => $validated['order_address'] ?? '',
                'message'    => $msg,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            ContactService::sendNotificationMail([
                'name'         => $validated['order_name'],
                'email'        => $validated['order_email'] ?? '',
                'phone'        => $validated['order_phone'],
                'address'      => $validated['order_address'] ?? '',
                'message'      => $validated['order_message'] ?? '',
                'product_name' => $validated['order_title_prd'],
                'type_name'    => 'Đặt mua / Yêu cầu báo giá sản phẩm',
                'created_at'   => now(),
            ]);

            return response()->json([
                'message' => 'Xử lý yêu cầu thành công',
                'code'    => 'success'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Ajax buyNow error: ' . $th->getMessage());
            return response()->json([
                'message' => 'Có vấn đề xảy ra trong quá trình xử lý: ' . $th->getMessage(),
                'code'    => 'error'
            ]);
        }

    }

    
}
