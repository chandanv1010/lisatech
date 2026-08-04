# Hướng dẫn quản trị Lisatech

Tài liệu này ghi lại các thao tác thường ngày mà quản trị viên cần làm, kèm giải
thích **vì sao** hệ thống hoạt động như vậy — để tránh hiểu là lỗi.

---

## 1. Thêm một nhóm sản phẩm vào menu ngang trên trang chủ

### Cần hiểu trước

Menu ngang **không tự sinh** từ danh sách nhóm sản phẩm. Đây là hai thứ khác nhau:

| | Chức năng | Tác dụng |
|---|---|---|
| Nội dung | **Quản lý nhóm sản phẩm** | Tạo nhóm, tạo trang danh mục, sản phẩm nằm trong nhóm |
| Điều hướng | **Quản lý menu** | Quyết định mục nào xuất hiện trên menu ngang, theo thứ tự nào |

Vì vậy, tạo nhóm sản phẩm và bật "Hoạt động" chỉ làm **trang nhóm đó tồn tại và
truy cập được**. Muốn nó xuất hiện trên menu ngang thì phải khai báo thêm ở
**Quản lý menu**. Đây là thiết kế có chủ ý: menu ngang chỉ nên có vài mục chính,
trong khi hệ thống đang có hàng chục nhóm sản phẩm.

> Cách kiểm tra nhanh nhóm đã tạo đúng chưa: mở trực tiếp đường dẫn của nhóm.
> Ví dụ nhóm "Bộ lưu điện cửa cuốn 3 pha Soji" có đường dẫn
> `bo-luu-dien-cua-cuon-3-pha-soji-pc1171` → mở
> `https://lisatech.vn/bo-luu-dien-cua-cuon-3-pha-soji-pc1171.html`.
> Nếu trang mở được thì nhóm hoàn toàn bình thường, chỉ còn thiếu bước thêm vào menu.

### Các bước thêm vào menu ngang

1. Vào **Quản lý menu** (`/menu/index`).
2. Tìm vị trí hiển thị có từ khóa **`main`** — đây là menu ngang của trang chủ.
   Bấm nút **sửa** (biểu tượng bút chì màu xanh).
3. Trong danh sách menu bên phải, tìm mục cha muốn thêm vào — ví dụ
   **Sản phẩm** — rồi bấm nút **Quản lý menu con** của mục đó.
   *(Nếu cần thêm một mục ở cấp cao nhất, dùng nút **Cập nhật Menu cấp 1** ở đầu
   danh sách thay vì bước này.)*
4. Ở cột bên trái có các khối tìm kiếm theo module. Bấm mở khối
   **Nhóm Sản Phẩm**.
5. Gõ từ khoá vào ô tìm kiếm (ví dụ `Soji`). Danh sách kết quả hiện ra ngay bên
   dưới sau khoảng một giây.
6. Bấm vào nhóm cần thêm. Nó sẽ được đưa sang danh sách bên phải, gồm 3 cột:
   - **Tên Menu** — chữ hiển thị trên menu (sửa được, không cần giống tên nhóm)
   - **Đường dẫn** — điền tự động, **không nên sửa tay**
   - **Vị trí** — số thứ tự sắp xếp
7. Bấm **Lưu lại**.
8. Mở lại trang chủ để kiểm tra. Nếu chưa thấy, xoá cache trình duyệt
   (`Ctrl` + `F5`).

### Lưu ý về cột "Vị trí"

Các mục được sắp xếp theo **Vị trí giảm dần** — số **lớn hơn thì nằm trước**.
Ví dụ menu `main` hiện tại: Giới thiệu = 7, Sản phẩm = 6, Giải pháp = 5,
Lĩnh vực = 4, Dịch vụ = 3, Tin tức = 2, Chính sách = 1.

Muốn chèn một mục vào giữa mà không phải sửa hết, hãy đánh số cách nhau
(10, 20, 30…) ngay từ đầu.

---

## 2. Thêm một nhóm bài viết vào menu ngang

Hoàn toàn giống mục 1, chỉ khác ở **bước 4**: mở khối **Nhóm Bài Viết** thay vì
**Nhóm Sản Phẩm**.

Ví dụ để đưa nhóm "Cảng biển đặc thù" vào dropdown **Lĩnh vực**: mở phần quản lý
mục con của mục **Lĩnh vực**, tìm trong khối **Nhóm Bài Viết**, thêm và lưu.

---

## 3. Vì sao có nhóm con hiện tự động, có nhóm thì không?

Trên menu ngang, dropdown **Thiết bị nguồn** hiện các nhóm con mà không ai khai
báo trong "Quản lý menu". Đó là một cơ chế tự động: khi đường dẫn của một mục
menu **khớp chính xác** với đường dẫn của một nhóm sản phẩm, hệ thống sẽ tự chèn
các nhóm con của nhóm đó vào menu.

Cơ chế này **chỉ chạy được khi đường dẫn khớp tuyệt đối**. Sau khi chuyển dữ liệu
từ website cũ, phần lớn đường dẫn menu không còn khớp với đường dẫn nhóm:

| Mục menu | Đường dẫn trong menu | Đường dẫn thật của nhóm | Khớp? |
|---|---|---|---|
| Thiết bị nguồn | `thiet-bi-nguon` | `thiet-bi-nguon` | ✅ nên tự chèn được |
| Ắc quy | `ac-quy-pc1157-html` | `ac-quy-pc1157` | ❌ dư `-html` |
| Sản phẩm | `san-pham-san-pham-c74` | `san-pham/san-pham/c74` | ❌ gạch ngang vs gạch chéo |
| Thiết bị, linh kiện thang máy | `san-pham-thiet-bi-linh-kien-thang-may-c82` | `san-phamthiet-bi-linh-kien-thang-mayc82` | ❌ lệch dấu gạch |

Đây là lý do các dropdown khác đều phải khai báo mục con bằng tay. **Không ảnh
hưởng gì đến người truy cập** — các mục khai báo tay vẫn chạy đúng. Nếu sau này
muốn menu tự sinh hoàn toàn, cần một đợt dọn dữ liệu đường dẫn; hãy trao đổi
trước vì việc đó sẽ làm một số nhóm sản phẩm hiện thêm vào menu.

---

## 4. Xem danh sách khách hàng liên hệ

Vào **Quản lý liên hệ** (`/contact/index`). Bảng có các cột: Họ Tên, **Loại**,
Ngày tạo, Số điện thoại, **Email**, Địa chỉ, Lời nhắn.

Cột **Loại** cho biết khách đến từ đâu:

| Loại | Nguồn |
|---|---|
| Yêu cầu báo giá | Nút "Yêu cầu báo giá" trên trang chủ / trang sản phẩm |
| Liên hệ kinh doanh | Nút "Liên hệ kinh doanh" → form ở trang Liên hệ |
| Đặt hàng | Form thanh toán trong giỏ hàng |
| Website cũ | Dữ liệu chuyển từ website cũ |

Các liên hệ gửi **trước** bản cập nhật này chưa có thông tin phân loại nên sẽ
hiện là **Không rõ**. Từ bản này trở đi mọi liên hệ mới đều được phân loại.

---

## 5. Trạng thái "Hoạt động" / "Không hoạt động"

Khi thêm hoặc sửa bài viết, nhóm bài viết, sản phẩm, nhóm sản phẩm — nhớ chọn
**Tình trạng = Hoạt động** rồi mới lưu. Nội dung để "Không hoạt động" sẽ **không
hiện trên website** dù đã lưu thành công.

---

## 6. Đổi icon hiển thị trên tab trình duyệt (favicon)

Mặc định hệ thống tự dùng dấu hiệu **LiSA** cắt ra từ logo công ty. Muốn thay
bằng ảnh riêng: vào **Cấu hình hệ thống**, tìm mục **`homepage_favicon`**, tải lên
ảnh vuông (khuyến nghị 512×512, định dạng PNG) rồi lưu. Ảnh này sẽ được ưu tiên
dùng thay cho ảnh mặc định.
