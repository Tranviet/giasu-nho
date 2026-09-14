# Roadmap & Tiến độ dự án "Gia Sư Nhỏ" (Little Tutor)

Tài liệu này ghi nhận trạng thái thực tế của dự án: các bước đã hoàn thành, đang thực hiện, và các bước tiếp theo theo từng phiên bản.

---

## Trạng thái hiện tại: Đã hoàn thành toàn bộ Backend Laravel API (V1 - MVP)

---

## 1. Các bước ĐÃ LÀM (Completed)

### Giai đoạn 1: Chuẩn bị & Kiến trúc
- [x] **Xây dựng & Thẩm định `plan.md`**:
  - Hoàn thiện tài liệu kiến trúc kỹ thuật và phân kỳ tính năng V1 -> V4.
  - Chuẩn hóa cấu hình model Anthropic Claude (`claude-3-7-sonnet-20250219` / `claude-3-5-sonnet-20241022` / `claude-3-5-haiku-20241022`).
  - Thiết lập cơ chế nén ảnh chụp bài tập tiểu học (max 1600px, JPEG 80%) trước khi gửi Claude Vision.
  - Tinh chỉnh schema: bổ sung `user_id`, `status`, `latency_ms` vào `ai_interactions`, `avatar` cho trẻ em, `exercise_id` nullable cho bài tập tự do.
  - Bổ sung nguyên tắc phản hồi sư phạm tích cực "Sandwich" và chống spam rate-limiting.
- [x] **Khởi tạo hệ sinh thái tài liệu `.memory/`**:
  - `roadmap_and_steps.md`: Theo dõi tiến độ chi tiết.
  - `system_architecture.md`: Đặc tả kiến trúc kỹ thuật, schema, luồng dữ liệu.
  - `api_specifications.md`: Đặc tả chi tiết các RESTful API endpoints `/api/v1/...`.

### Giai đoạn 2: Triển khai Backend Laravel 11 API
- [x] **Khởi tạo dự án Laravel 11 API sạch**:
  - PHP 8.3+, Laravel 11 Framework với Laravel Sanctum API token authentication.
  - Cài đặt và cấu hình thư viện `intervention/image` (v4.3, GD driver) phục vụ tối ưu hóa hình ảnh.
  - Thiết lập symlink storage: `php artisan storage:link`.
  - Cấu hình file `.env` và `config/services.php` cho Anthropic API (`ANTHROPIC_API_KEY`, `ANTHROPIC_MODEL`).
- [x] **Database Migrations & Eloquent Models**:
  - Đã tạo và migrate thành công 10 thực thể:
    1. `users`: Tài khoản phụ huynh (name, email, password).
    2. `children`: Hồ sơ con (`user_id`, `name`, `grade` 1-5, `avatar`).
    3. `subjects`: Danh mục môn học (Toán học, Tiếng Việt).
    4. `topics`: Chủ đề GDPT 2018 theo khối lớp.
    5. `exercises`: Kho bài tập trắc nghiệm và điền từ (JSON content, độ khó).
    6. `exercise_submissions`: Lịch sử nộp bài, kết quả chấm đúng/sai, ảnh chụp vở và feedback AI.
    7. `plans`: Gói cước (`free`, `basic`, `premium`) và `monthly_ai_quota`.
    8. `subscriptions`: Trạng thái gói, chu kỳ `current_period_start` và `current_period_end`.
    9. `ai_interactions`: Log toàn bộ lượt gọi AI (tokens input/output, latency_ms, estimated_cost_usd, status).
    10. `child_topic_mastery`: Điểm thành thạo từng chủ đề (0 - 100).
- [x] **Phân quyền & Bảo mật (Policy & Isolation)**:
  - Viết `ChildPolicy`: Ngăn chặn triệt để rò rỉ dữ liệu giữa các gia đình (Parent A không thể xem, sửa, xóa con của Parent B).
- [x] **Tầng nghiệp vụ (Service Layer)**:
  - `ImageService`: Scale down ảnh chụp bài tập xuống max 1600px, nén JPEG 80% trước khi base64 và gửi Claude Vision.
  - `QuotaService`: Kiểm tra hạn mức gọi AI trong chu kỳ gói của user, chặn khi vượt quá quota (HTTP 402).
  - `ClaudeService`: Tích hợp Anthropic API với timeout 60s, system prompt sư phạm tiểu học ("Sandwich feedback"), trích xuất JSON có cấu trúc `{ overall_feedback, items }`, đo latency và token cost.
- [x] **Controllers, Form Requests & API Resources (`/api/v1/`)**:
  - `AuthController`: Register (tự cấp gói Free), Login, Logout, Me.
  - `ChildController`: CRUD hồ sơ con, bảo vệ bởi `ChildPolicy`.
  - `CurriculumController`: Lấy danh sách môn học, chủ đề theo lớp, bài tập trong chủ đề; nộp bài chấm tự động và tính điểm mastery score.
  - `AiController`: Chấm bài qua ảnh chụp vở (`/api/v1/ai/homework-check`) và hỏi đáp bằng văn bản chuyển từ giọng nói (`/api/v1/ai/qa`), throttle 5 reqs/min.
  - `ProgressController`: Báo cáo tiến độ học tập, tổng bài làm, tỷ lệ chính xác và lịch sử bài tập của bé.
- [x] **Database Seeders**:
  - `SubjectSeeder`: Môn Toán học & Tiếng Việt.
  - `TopicSeeder`: 9 chủ đề mẫu chuẩn khung GDPT 2018 cho Lớp 1 & Lớp 2.
  - `PlanSeeder`: 3 gói cước mẫu (Free: 10 lượt, Basic: 100 lượt, Premium: 300 lượt).
  - `ExerciseSeeder`: Các câu hỏi trắc nghiệm và điền khuyết kèm lời giải chi tiết.
- [x] **Automated Feature Tests**:
  - `AuthTest`: 5 test cases kiểm tra đăng ký, đăng nhập, validation lỗi, cấp token, xem profile me và logout.
  - `ChildManagementTest`: Kiểm tra CRUD hồ sơ con và phân quyền cách ly gia đình (Parent A không thể truy cập con Parent B).
  - `CurriculumAndExerciseTest`: Kiểm tra duyệt môn/chủ đề công khai, nộp bài trắc nghiệm tự động chấm đúng/sai và tính điểm mastery.
  - `AiTutoringTest`: Kiểm tra hỏi đáp AI, upload ảnh bài tập chấm điểm, chặn gọi khi hết quota (HTTP 402), xem tiến độ bé.
  - **Kết quả: 16/16 tests PASS, 83 assertions thành công 100%.**

### Giai đoạn 3: Xây dựng Giao diện Màn hình Chính Bé Học (Child Dashboard - V1)
- [x] **Thiết kế & Dựng Hệ sinh thái Reusable Blade Components (iPad-First)**:
  - `child-layout.blade.php`: Khung giao diện responsive (iPad Portrait 768×1024, iPad Landscape 1024×768, Mobile, Desktop), nền pastel ấm áp, safe-area padding chống che khuất.
  - `greeting-header.blade.php`: Lời chào cá nhân hóa theo thời gian trong ngày (sáng/chiều/tối), avatar bé (64px+), chip Lớp học, bộ đếm Sao thưởng ⭐ và ngọn lửa chuỗi học tập liên tục 🔥.
  - `ai-tutor-entry.blade.php`: Điểm chạm AI Tutor nổi bật dạng Bạn Cú Nhỏ Gia Sư đồng hành, hiệu ứng nổi bồng bềnh, nút bấm xúc giác 56px+ ("Chụp ảnh bài tập", "Hỏi bằng giọng nói").
  - `subject-card.blade.php`: Thẻ môn học khổ lớn với vòng tròn tiến độ (Progress Ring), hỗ trợ trạng thái đang học (Toán học, Tiếng Việt) và trạng thái "Sắp ra mắt" / khóa (Tiếng Anh, Khoa học, Đọc sách).
  - `progress-ring.blade.php`: Vòng tròn SVG hiển thị phần trăm trực quan sinh động.
  - `recent-lesson.blade.php`: Thẻ "Tiếp tục bài học dở dang" kèm thanh tiến độ 3/5 câu và nút "Học tiếp ngay 🚀" 56px+.
  - `reward-badge.blade.php`: Thanh tiến độ mục tiêu 5 bài/ngày và bộ sưu tập huy hiệu học tập.
  - `bottom-nav.blade.php`: Thanh điều hướng cố định chân trang chuẩn iPad với 4 icon lớn min 56px.
  - `ai-dialog-modal.blade.php`: Bảng tương tác thông minh cho phép bé thử nghiệm hỏi đáp giọng nói và chụp ảnh tải lên.
- [x] **Hệ thống Design Tokens & Trải nghiệm Xúc giác (Touch Experience)**:
  - Bảng màu hài hòa cho trẻ nhỏ: Tím (Gia sư), Xanh dương (Toán), Xanh lá (Tiếng Việt), Vàng mật ong (Sao), Cam (Chuỗi).
  - Nút bấm 3D tactile buttons (`border-b-4`, `active:translate-y-1`, `active:scale-95`).
  - Âm thanh phản hồi tích hợp Web Audio API (phát tiếng chuông chime vui tai khi bấm nút hoặc nhận thưởng mà không cần tải file âm thanh ngoài).
- [x] **Controller & Routes**:
  - `ChildDashboardController.php`: Kết nối dữ liệu thực từ database (`Subject::withCount('topics')`), hiển thị các môn sắp ra mắt theo đúng yêu cầu.
  - `routes/web.php`: Đăng ký route `/` và `/child-dashboard`.
- [x] **Kiểm thử tự động**:
  - `ChildDashboardTest.php`: Đảm bảo trang tải mượt mà với profile demo lẫn phụ huynh đã đăng nhập.
  - 18/18 tests PHPUnit / Pest PASS (92 assertions).
  - Build Vite / Tailwind v4 hoàn tất trong 190ms (`npm run build`).

### Giai đoạn 4: Xây dựng Trang Chủ & Xác thực Phụ huynh (Homepage & Web Auth)
- [x] **Trang chủ Giới thiệu Sản phẩm (Homepage)**:
  - Header thương hiệu: Logo Cú Thông Thái, liên kết tính năng, môn học, bảng giá, đăng nhập, đăng ký và nút vào góc học của bé.
  - Hero Section: Thông điệp cốt lõi, nút CTA nổi bật dẫn tới Góc học của bé và form đăng ký.
  - Bảng mô phỏng trực quan (Interactive Tablet Showcase): Mô phỏng Gia Sư AI đang chấm bài tập vở ô ly và phản hồi sư phạm tích cực.
  - Bộ 4 tính năng nổi bật: Chấm bài ảnh, Sư phạm Sandwich, Hỏi đáp giọng nói, Báo cáo tiến độ cho ba mẹ.
  - Giới thiệu chương trình GDPT 2018: Toán & Tiếng Việt Lớp 1 - 5.
  - Bảng so sánh 3 gói cước: Miễn phí (0đ), Gia Đình Cơ Bản (99.000đ/tháng), Gia Đình Nâng Cao (199.000đ/tháng).
- [x] **Hệ thống Xác thực Phụ huynh (Login & Register)**:
  - Popup Modal tương tác ngay trên trang chủ: Cho phép đăng nhập/đăng ký một chạm mà không cần tải lại trang.
  - Trang đăng nhập chuyên dụng: `/login` (ghi nhớ đăng nhập, thông báo lỗi rõ ràng).
  - Trang đăng ký chuyên dụng: `/register` (nhập thông tin phụ huynh + tạo nhanh hồ sơ con và tự động cấp gói Miễn phí).
  - Đăng xuất an toàn: `POST /logout` (xóa session và quay về trang chủ).
  - Tự động chuyển hướng phụ huynh vào Góc học của bé (`/child-dashboard`) sau khi đăng nhập hoặc đăng ký thành công.
- [x] **Kiểm thử tự động Web Auth**:
  - `WebAuthTest.php`: Đảm bảo 100% các luồng đăng ký, đăng nhập, phân quyền session và đăng xuất hoạt động chuẩn xác.
  - Tổng cộng toàn dự án: **23/23 tests passed** (115 assertions).

---

## 2. Các bước ĐANG THỰC HIỆN (In Progress)
- [x] Đã hoàn thành Trang chủ (Homepage) và Hệ thống Đăng nhập / Đăng ký phụ huynh. Trang đã sẵn sàng tại `https://giasu.local/`.

---

## 3. Các bước SẼ LÀM (Upcoming)

### Giai đoạn tiếp theo:
- [ ] **Màn hình Làm bài tập tương tác (Interactive Quiz / Exercise)**:
  - Giao diện làm trắc nghiệm và điền khuyết với font chữ to, âm thanh chúc mừng khi trả lời đúng.
- [ ] **Màn hình Chấm bài AI Vision thực tế (Live Camera & Homework Check)**:
  - Chụp ảnh từ camera tablet/webcam và gửi lên endpoint AI chấm bài thực tế.
- [ ] **Admin Panel (Filament Admin)**:
  - Quản trị nội dung câu hỏi và danh mục.

---

*Cập nhật lần cuối: 2026-09-14 - Phiên bản: Backend v1.0.0-done (16/16 tests passed)*
