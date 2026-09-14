# Kiến Trúc Hệ Thống — Backend "Gia Sư Nhỏ" (Little Tutor)

## 1. Tổng quan Kiến trúc

- **Framework**: Laravel 11 (PHP 8.3+)
- **Kiến trúc**: Stateless RESTful JSON API
- **Xác thực (Authentication)**: Laravel Sanctum (Personal Access Token)
- **Cơ sở dữ liệu**: MySQL / SQLite (chuẩn hóa qua Laravel Migrations)
- **Xử lý đồ họa**: Intervention Image (GD driver) — nén & resize ảnh bài tập về max 1600px, 80% JPEG
- **Tích hợp AI**: Anthropic Claude API Messages endpoint (`claude-3-7-sonnet-20250219` / `claude-3-5-sonnet-20241022`) với HTTP timeout 60s
- **Hạn mức (Quota)**: Quota đếm theo số lượng request thành công trong chu kỳ gói của gia đình (`user_id`).

---

## 2. Sơ đồ dữ liệu (Entity Relationship Overview)

```
[users] (Phụ huynh)
   │
   ├──< [children] (Hồ sơ các con: Lớp 1-5, avatar mascot)
   │       │
   │       ├──< [exercise_submissions] (Lịch sử làm bài: đúng/sai, ảnh, feedback AI)
   │       ├──< [child_topic_mastery] (Điểm làm chủ từng chủ đề GDPT 2018)
   │       └──< [ai_interactions] (Log các lượt gọi AI: vision/qa, tokens, cost)
   │
   └──< [subscriptions] >── [plans] (Gói cước & monthly_ai_quota)

[subjects] (Môn học: Toán, Tiếng Việt)
   │
   └──< [topics] (Chủ đề theo khối lớp)
           │
           └──< [exercises] (Kho câu hỏi trắc nghiệm, điền khuyết)
```

---

## 3. Quy trình xử lý nghiệp vụ chính

### A. Luồng Chấm bài bằng Ảnh (AI Homework Check)
1. **Client** (iPad/Web): Gửi multipart form data (`image` + `child_id`) lên `/api/v1/ai/homework-check`.
2. **Middleware / Policy**:
   - Xác thực Sanctum token của phụ huynh.
   - Kiểm tra quyền sở hữu đối với `child_id`.
   - Rate limit: tối đa 5 requests/phút/child.
3. **Quota Check**:
   - `QuotaService` kiểm tra xem tài khoản phụ huynh còn lượt gọi AI trong chu kỳ cước không.
   - Nếu hết: Trả về `402 Payment Required` kèm thông báo cần nâng cấp gói.
4. **Image Processing**:
   - `ImageService` nén & resize ảnh xuống max 1600px cạnh dài, chất lượng 80% JPEG.
   - Lưu ảnh vào storage và chuyển đổi thành chuỗi base64.
5. **Claude Vision Invocation**:
   - Gửi request đến Anthropic API với system prompt chuyên sâu về đọc chữ viết tay tiểu học tiếng Việt, yêu cầu định dạng phản hồi JSON:
     `{ "overall_feedback": "...", "items": [{ "question": "...", "is_correct": true, "explanation": "..." }] }`
6. **Logging & Response**:
   - Ghi nhận `ai_interactions` (input_tokens, output_tokens, estimated_cost_usd, latency_ms, status='success').
   - Lưu kết quả vào `exercise_submissions`.
   - Trả về JSON chuẩn cho client.

### B. Luồng Hỏi đáp AI (AI Q&A Chat)
1. **Client**: Thu âm giọng nói bé qua Web Speech API trên trình duyệt $\rightarrow$ chuyển thành văn bản $\rightarrow$ gửi `POST /api/v1/ai/qa` (`question` + `child_id`).
2. **Quota Check & Rate Limit**: Tương tự như trên.
3. **Claude Service**:
   - Đưa câu hỏi vào Claude kèm ngữ cảnh bé học lớp mấy.
   - Trả lời bằng giọng điệu gần gũi, ấm áp, khuyến khích tư duy độc lập.
4. **Logging & Response**: Ghi nhận log và trả về nội dung trả lời.
