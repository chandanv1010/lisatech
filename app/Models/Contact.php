<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'id',
        'name',
        'phone',
        // Thiếu 'email' ở đây là lý do email khách gửi lên không bao giờ được
        // lưu: cột có thật trong bảng, saveContact có truyền lên, nhưng gán
        // hàng loạt lặng lẽ bỏ mọi khoá không được liệt kê. Cùng loại lỗi mà
        // dự án này từng dính với 'pubish' và 'parentid'.
        'email',
        'address',
        'product_id',
        'post_id',
        'publish',
        'created_at',
        'type',
        'message',
        'status',
        'handled_by',
    ];

    protected $table = 'contacts';

    public function products(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function posts(){
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    /**
     * Người đánh dấu liên hệ này là đã xử lý.
     *
     * Rỗng khi liên hệ chưa được xử lý, và được xoá đi nếu ai đó chuyển ngược
     * về chưa xử lý - giữ lại tên cũ sẽ thành lời khai sai về việc ai đang phụ
     * trách.
     */
    /**
     * Tên sản phẩm mà khách hỏi tư vấn, nếu liên hệ này gắn với một sản phẩm.
     *
     * Tên nằm ở bảng ngôn ngữ chứ không nằm trên products, nên phải đi qua
     * pivot. Trả null khi liên hệ không gắn sản phẩm nào.
     */
    public function getProductNameAttribute(): ?string
    {
        $ngonNgu = $this->products?->languages?->first();

        return $ngonNgu?->pivot?->name;
    }

    public function handler(){
        return $this->belongsTo(User::class, 'handled_by', 'id');
    }

}