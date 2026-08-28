@php
    $nhanTrangThai = config('apps.general.contactStatus', []);
    $chuaXuLy = (int) config('apps.general.contactStatusPending', 1);
    $gioiHanLoiNhan = 110;
@endphp

{{-- Bọc để bảng cuộn ngang trong khung thay vì tràn ra ngoài màn hình. --}}
<div class="table-responsive contact-table">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th class="col-check">
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>Họ Tên</th>
            <th class="col-nowrap">Loại</th>
            <th class="col-nowrap">Ngày tạo</th>
            <th class="col-nowrap">Số điện thoại</th>
            <th>Email</th>
            <th class="col-message">Lời nhắn</th>
            <th class="col-nowrap">Trạng thái</th>
            <th class="col-nowrap">Người xử lý</th>
            <th class="text-center col-nowrap">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($contacts) && is_object($contacts))
            @foreach($contacts as $contact)
                @php
                    $dangCho = (int) $contact->status === $chuaXuLy;

                    // Cố ý KHÔNG in thô nội dung này nữa. Đây là chữ do khách
                    // gửi lên từ form ngoài trang, mà view cũ dùng {!! !!} nên
                    // thẻ HTML trong đó chạy thật trong trang quản trị - hiện
                    // đã có 9 lời nhắn spam chèn liên kết sống được vào đây.
                    $loiNhan = trim((string) $contact->message);
                    $daiQua = mb_strlen($loiNhan) > $gioiHanLoiNhan;
                @endphp
                <tr class="contact-row {{ $dangCho ? 'contact-row--pending' : '' }}" data-contact-id="{{ $contact->id }}">
                    <td>
                        <input type="checkbox" value="{{ $contact->id }}" class="input-checkbox checkBoxItem">
                    </td>

                    <td>
                        {{ $contact->name }}
                        {{-- Dòng phụ dưới tên. Ưu tiên sản phẩm khách hỏi tư vấn,
                             không có thì hiện địa chỉ. Cả hai đều thưa dữ liệu nên
                             không đáng một cột riêng, mà nằm đây thì vẫn thấy. --}}
                        @if($contact->product_name)
                            <div class="contact-sub contact-sub--product">
                                <i class="fa fa-tag"></i> {{ $contact->product_name }}
                            </div>
                        @elseif(trim((string) $contact->address) !== '')
                            <div class="contact-sub">{{ $contact->address }}</div>
                        @endif
                    </td>

                    <td class="col-nowrap">
                        {{ config('apps.general.contactType')[$contact->type] ?? 'Không rõ' }}
                    </td>

                    <td class="col-nowrap">
                        {{ convertDateTime($contact->created_at,'d/m/Y') }}
                    </td>

                    <td class="col-nowrap">
                        {{ $contact->phone }}
                    </td>

                    <td class="col-email">
                        {{ $contact->email }}
                    </td>

                    <td class="col-message">
                        <div class="contact-msg" @if($daiQua) data-day="{{ $loiNhan }}" @endif>{{ $daiQua ? mb_substr($loiNhan, 0, $gioiHanLoiNhan).'…' : $loiNhan }}</div>
                        @if($daiQua)
                            <button type="button" class="contact-msg__more">xem thêm</button>
                        @endif
                    </td>

                    <td class="col-nowrap">
                        <select class="form-control input-sm js-contact-status" data-contact-id="{{ $contact->id }}">
                            @foreach($nhanTrangThai as $giaTri => $nhan)
                                <option value="{{ $giaTri }}" {{ (int) $contact->status === (int) $giaTri ? 'selected' : '' }}>
                                    {{ $nhan }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td class="js-contact-handler col-nowrap">
                        {{ $contact->handler->name ?? '' }}
                    </td>

                    <td class="text-center col-nowrap">
                        <a href="{{ route('contact.delete', $contact->id) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
</div>
{{  $contacts->links('pagination::bootstrap-4') }}
