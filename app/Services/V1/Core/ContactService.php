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
            // Liên hệ chưa xử lý phải nằm trên đầu. Quy ước 1 = chưa, 2 = rồi
            // nên xếp tăng dần là đủ, không cần biểu thức sắp xếp đặc biệt.
            // Trong mỗi nhóm thì mới nhất lên trước, giữ đúng nếp cũ của trang.
            [['status', 'ASC'], ['id', 'DESC']],
        );

        // Nạp sẵn bằng vài truy vấn, thay vì để mỗi dòng trong bảng tự đi hỏi
        // cơ sở dữ liệu một lần. Tên sản phẩm nằm ở bảng ngôn ngữ nên phải
        // nạp kèm quan hệ đó.
        $contacts->getCollection()->load(['handler', 'products.languages']);

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

    /**
     * Đổi trạng thái xử lý của một liên hệ.
     *
     * Người xử lý được ghi cùng lúc chứ không phải chọn tay: người bấm nút
     * chính là người xử lý. Chuyển ngược về "chưa xử lý" thì xoá luôn tên đi,
     * vì giữ lại sẽ thành lời khai sai rằng ai đó đang phụ trách việc này.
     *
     * @param  int  $userId  người đang đăng nhập
     * @return array|null  dữ liệu để bảng cập nhật lại đúng dòng vừa đổi
     */
    public function updateHandlingStatus(int $id, int $status, ?int $userId): ?array
    {
        // PHP tự chuyển khoá mảng dạng số về kiểu integer, nên khoá của
        // contactStatus là 1 và 2 chứ không phải '1' và '2'. So sánh chặt với
        // chuỗi ở đây sẽ không bao giờ khớp và mọi cập nhật đều bị từ chối.
        $trangThaiHopLe = array_map('intval', array_keys(config('apps.general.contactStatus', [])));

        if (!in_array($status, $trangThaiHopLe, true)) {
            return null;
        }

        $daXuLy = $status === (int) config('apps.general.contactStatusDone');

        DB::beginTransaction();

        try {
            $contact = $this->contactRepository->findById($id);

            if ($contact === null) {
                DB::rollBack();

                return null;
            }

            $contact->status = $status;
            $contact->handled_by = $daXuLy ? $userId : null;
            $contact->save();

            DB::commit();

            $contact->load('handler');

            return [
                'id' => $contact->id,
                'status' => (int) $contact->status,
                'status_label' => config('apps.general.contactStatus')[$contact->status] ?? '',
                'handler_name' => $contact->handler->name ?? '',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('ContactService updateStatus error: ' . $e->getMessage());

            return null;
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
            'message',
            'status',
            // Cần có mặt ở đây thì quan hệ handler mới nạp được: truy vấn này
            // chọn cột tường minh, thiếu khoá ngoại là quan hệ trả về rỗng.
            'handled_by',
        ];
    }
}
