<?php

namespace App\Services\V1\Core;
use Illuminate\Support\Facades\DB;
use App\Mail\ContactMail;

use App\Services\V1\BaseService;

use App\Repositories\Core\ContactRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Post\PostRepository;

class ContactService extends BaseService 
{
    protected $contactRepository;
    protected $productRepository;
    protected $postRepository;

    public function __construct(
        ContactRepository $contactRepository,
        ProductRepository $productRepository,
        PostRepository $postRepository
    ){
        $this->contactRepository = $contactRepository;
        $this->productRepository = $productRepository;
        $this->postRepository = $postRepository;
    }

    public function paginate($request){
        // Default to '' before addslashes(): on a first page load there is no
        // keyword in the query string, and PHP 8.4 deprecates passing null.
        $condition['keyword'] = addslashes($request->input('keyword') ?? '');
        $perPage = $request->integer('perpage');
        $contacts = $this->contactRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'contact/index'], 
        );
        return $contacts;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->except('_token');

            $payload['name'] = $request->input('name') ?? $request->input('fullname') ?? 'Khách hàng';
            $contact = $this->contactRepository->create($payload);
            $product_name = ($contact->product_id != null) ? $this->productRepository->getProductById($contact->product_id, 1)->name : null;
            $post_name = ($contact->post_id != null) ?  $this->postRepository->getPostById($contact->post_id, 1)->name : null;

            $typeName = isset(config('apps.general.contactType')[$contact->type]) 
                ? config('apps.general.contactType')[$contact->type] 
                : 'Yêu cầu tư vấn / Liên hệ';

            $data = [
                'name' => $contact->name ?? 'Khách hàng',
                'email' => $contact->email ?? '',
                'phone' => $contact->phone ?? '',
                'address' => $contact->address ?? '',
                'message' => $contact->message ?? '',
                'type_name' => $typeName,
                'type' => $contact->type ?? null,
                'product_id' => $contact->product_id ?? null,
                'product_name' => $product_name ?? $post_name,
                'post_id' => $post_name,
                'created_at' => $contact->created_at ?? now(),
            ];

            self::sendNotificationMail($data);

            DB::commit();
            return [
                'code' => 10,
                'message' => 'Gửi liên hệ thành công , Chúng tôi sẽ sớm phản hồi lại bạn'
            ];
        }catch(\Exception $e ){
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('ContactService create error: ' . $e->getMessage());
            return [
                'code' => 11,
                'message' => 'Có vấn đề xảy ra! Hãy thử lại'
            ];
        }
    }

    public static function sendNotificationMail($data)
    {
        try {
            $recipients = array_values(array_unique(filter_var_array([
                'tuannc.dev@gmail.com',
                'contact@lisatech.vn',
                'lisatech3103@gmail.com',
                config('mail.from.address'),
            ], FILTER_VALIDATE_EMAIL)));

            if (!empty($recipients)) {
                \Illuminate\Support\Facades\Mail::to($recipients)->send(new \App\Mail\ContactMail([
                    'name' => $data['name'] ?? 'Khách hàng',
                    'email' => $data['email'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'address' => $data['address'] ?? '',
                    'message' => $data['message'] ?? '',
                    'product_name' => $data['product_name'] ?? null,
                    'type_name' => $data['type_name'] ?? null,
                    'type' => $data['type'] ?? null,
                    'product_id' => $data['product_id'] ?? null,
                    'post_id' => $data['post_id'] ?? null,
                    'created_at' => $data['created_at'] ?? now(),
                ]));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contact mail notification error: ' . $e->getMessage());
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{
            $payload = $request->except(['_token','send']);
            $contact = $this->contactRepository->update($id, $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $contact = $this->contactRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        // No 'publish' here: the contacts table has never had that column in any
        // migration, so selecting it made the whole "Quản lý liên hệ" page fail
        // with "Unknown column 'publish' in 'field list'". It was a copy-paste
        // leftover from the services whose models really are publishable.
        return [
            'id',
            'name',
            'address',
            'phone',
            'email',
            'product_id',
            'post_id',
            'gender',
            'created_at',
            'type',
            'message'
        ];
    }
}
