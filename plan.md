# PLAN: Webapp Gia Sư AI — Toán & Tiếng Việt (Tiểu học)

> File này dùng làm ngữ cảnh (context) để đưa cho AI coding tool (Claude Code, Cursor, v.v.) dựng code từng phần. Phần cuối file là **prompt chi tiết cho backend Laravel** — dùng phần đó để bắt đầu.

---

## 1. Tổng quan sản phẩm

**Tên tạm thời:** Gia Sư Nhỏ

**Mô tả:** Webapp gia sư online cho học sinh tiểu học (lớp 1–5), tập trung 2 môn: **Toán** và **Tiếng Việt**. Học sinh làm bài tập, được AI (Claude) chấm và giải thích lỗi sai bằng lời lẽ thân thiện, có thể hỏi đáp bằng giọng nói. Có thể dùng bài tập cô giáo giao trên lớp (chụp ảnh) để được giảng giải cá nhân hóa.

**Đối tượng người dùng:**
- Học sinh: 6–10 tuổi, thao tác chủ yếu qua chạm (touch), chủ yếu dùng iPad/tablet.
- Phụ huynh: quản lý tài khoản con, xem báo cáo tiến độ, thanh toán subscription.
- (Giai đoạn sau) Giáo viên/trung tâm: nếu mở rộng B2B.

**Mô hình kinh doanh:** Freemium + subscription hàng tháng theo gia đình. Giới hạn số lượt hỏi AI/chấm bài theo gói để kiểm soát chi phí token.

**Thiết bị mục tiêu chính:** iPad/tablet qua trình duyệt Safari (webapp responsive, có thể "Add to Home Screen" như PWA), tương thích tốt trên desktop/Android.

---

## 2. Tính năng chính (theo giai đoạn)

### V1 — MVP
- Đăng ký/đăng nhập phụ huynh, tạo hồ sơ con (tên, lớp, môn quan tâm).
- Kho bài tập theo môn (Toán, Tiếng Việt) → theo lớp (1–5) → theo chủ đề.
- Bé làm bài tập trắc nghiệm/điền đáp án cơ bản, hệ thống tự chấm.
- Chấm bài tập bằng ảnh: chụp bài làm → Claude đọc, chấm, giải thích lỗi sai.
- Không cần đăng nhập để dùng thử 1 phần nội dung (demo/free trial).

### V2
- Hệ thống tài khoản đầy đủ, lưu tiến độ học theo từng bé.
- Hỏi đáp AI bằng giọng nói (Web Speech API ở frontend, backend chỉ nhận text).
- Dashboard phụ huynh: xem môn nào bé yếu/mạnh, thời gian học, lịch sử bài làm.
- Hệ thống thưởng: sao, huy hiệu, streak học mỗi ngày.
- Subscription trả phí (Stripe hoặc cổng nội địa MoMo/VNPay), giới hạn lượt dùng AI theo gói.

### V3
- Lộ trình học cá nhân hóa: hệ thống đề xuất bài tiếp theo dựa trên điểm yếu.
- Viết tay trên canvas (bé viết số/chữ bằng ngón tay), gửi ảnh canvas lên Claude để chấm.
- Mở rộng thêm môn (Khoa học/Tự nhiên-Xã hội).

### V4 (định hướng xa)
- Tính năng lớp/nhóm cho giáo viên/trung tâm (B2B).
- Mascot 2D biểu cảm sinh động hơn / cân nhắc avatar nâng cao nếu ngân sách cho phép.

---

## 3. Kiến trúc kỹ thuật tổng thể

- **Backend:** Laravel (REST API), MySQL/PostgreSQL, PHP 8.3+.
- **Frontend:** chưa chốt framework cụ thể — có thể Laravel Blade + Alpine.js (đơn giản, nhanh) hoặc Vue/React qua Inertia.js hoặc SPA riêng gọi API. **Quyết định này để sau, backend thiết kế theo hướng API thuần (stateless, token-based) để tương thích với bất kỳ lựa chọn frontend nào.**
- **AI Provider:** Anthropic Claude API (cấu hình qua `.env`, mặc định `claude-3-7-sonnet-20250219` hoặc `claude-3-5-sonnet-20241022` cho chấm bài ảnh/giải thích chi tiết, `claude-3-5-haiku-20241022` cho tác vụ nhanh/tối ưu chi phí).
- **Tiền xử lý ảnh (Image Preprocessing):** Nén và resize ảnh bài tập (max cạnh 1600px, JPEG quality 80-85%) trước khi chuyển sang Base64 gửi Claude Vision để tiết kiệm vision token và chống timeout HTTP.
- **Giọng nói:** xử lý ở phía trình duyệt (Web Speech API — STT + TTS), backend không cần xử lý audio, chỉ nhận/trả text.
- **Thanh toán:** Stripe (subscription), cân nhắc thêm MoMo/VNPay cho thị trường Việt Nam.
- **Lưu trữ ảnh bài tập:** S3-compatible storage (AWS S3 / Cloudflare R2 / DigitalOcean Spaces) qua Laravel Filesystem (local storage khi dev).
- **Auth:** Laravel Sanctum (API token, phù hợp cho SPA/mobile-friendly webapp).

---

## 4. Phác thảo dữ liệu (high-level, backend sẽ chi tiết hoá)

- `users` — phụ huynh (tài khoản chính, đăng nhập, thanh toán).
- `children` — hồ sơ con (tên, ngày sinh/lớp, avatar mascot mặc định, thuộc về 1 user).
- `subjects` — môn học (Toán, Tiếng Việt).
- `topics` — chủ đề trong môn, gắn với lớp (1–5) và chương trình khung GDPT 2018.
- `exercises` — bài tập gốc do AI sinh hoặc admin nhập, gắn với topic.
- `exercise_submissions` — bài làm của bé (đáp án chọn, hoặc ảnh chụp `image_path`, `exercise_id` có thể nullable nếu là bài tự chụp ngoài sách), kết quả chấm & feedback.
- `ai_interactions` — log các lượt hỏi đáp/chấm bài qua Claude (`user_id`, `child_id`, type, prompt/input_summary, ai_response, model_used, input_tokens, output_tokens, estimated_cost_usd, `status`, `latency_ms`) — dùng để kiểm soát chi phí, thống kê và giới hạn theo gói.
- `subscriptions` / `plans` — gói cước, giới hạn lượt dùng AI/tháng (`monthly_ai_quota`).
- `progress` hoặc `child_topic_mastery` — theo dõi mức độ thành thạo của bé theo từng chủ đề.
- `badges` / `child_badges` — hệ thống thưởng (sao, huy hiệu).

---

## 5. Nguyên tắc quan trọng cần AI tuân thủ khi code

- **Không vi phạm bản quyền:** Không copy nguyên văn bài tập từ sách giáo khoa có bản quyền — bài tập trong hệ thống là AI sinh mới hoặc admin tự soạn, chỉ dùng **chương trình khung GDPT 2018** (mục tiêu/chủ đề) làm tham chiếu.
- **Kiểm soát chi phí & Quota:** Mọi lượt gọi AI phải được log lại (`ai_interactions`) kèm token usage, và kiểm tra giới hạn gói cước **trước khi** gọi API.
- **Chống spam (Rate Limiting):** Trẻ em dùng tablet dễ bấm liên tục; cần throttle ngắn hạn (ví dụ: tối đa 5 requests/phút/child cho các endpoint gọi AI).
- **Tối ưu hóa hình ảnh:** Ảnh chụp từ tablet thường rất nặng (3-10MB). Phải resize/nén ảnh (max 1600px) trước khi gửi sang Claude Vision để tránh nghẽn băng thông và giảm chi phí token.
- **Sư phạm tích cực (Pedagogical Prompting):** Với học sinh tiểu học (đặc biệt lớp 1-2 viết tay bút chì mờ/nguệch ngoạc), prompt cho Claude phải có chỉ dẫn: kiên nhẫn đọc chữ viết tay tiếng Việt, áp dụng nguyên tắc phản hồi "Sandwich" (khen ngợi nỗ lực/nét chữ $\rightarrow$ nhẹ nhàng chỉ ra lỗi sai $\rightarrow$ hướng dẫn từng bước cách làm đúng).
- **Bảo mật & Quyền riêng tư:** Dữ liệu trẻ em nhạy cảm — cần thiết kế Policy phân quyền chặt: phụ huynh chỉ truy cập được hồ sơ con mình, không lộ dữ liệu chéo giữa các gia đình. Avatar trẻ em nên dùng mascot/icon có sẵn thay vì ảnh chân dung thật.

---

## 6. PROMPT DÀNH CHO AI CODING TOOL — BACKEND LARAVEL (dùng đầu tiên)

> Copy phần bên dưới (từ "Bạn là..." đến hết) để đưa cho AI coding tool (Claude Code, Cursor, v.v.) bắt đầu dựng backend.

```
You are a senior Laravel engineer. Help me scaffold the BACKEND ONLY (API-first, no frontend yet) for an education SaaS product called "Gia Su Nho" (Little Tutor).

PRODUCT CONTEXT:
An AI tutoring webapp for elementary school students (grades 1-5), covering two subjects: Math and Vietnamese language. Parents create an account and add child profiles. Children complete exercises in the system, or upload a photo of homework from their notebook; the backend calls the Anthropic Claude API to grade the work and explain mistakes in a friendly tone. There is a subscription system that limits the number of AI calls per month based on plan tier.

TECHNICAL REQUIREMENTS:
- Latest stable Laravel LTS, PHP 8.3+.
- Pure API architecture (no Blade views for UI — JSON responses only), using Laravel Sanctum for API token authentication.
- Database: MySQL (use standard Laravel migrations, no raw SQL).
- Code organization: thin controllers, business logic in Service classes or Actions, Form Requests for input validation, API Resources for JSON response formatting.
- Image handling: Resize and compress uploaded homework images (max 1600px width/height, 80% JPEG quality) before storing and converting to base64 for Claude Vision.
- Write Feature tests (Pest or PHPUnit) for the main endpoints.

SCOPE TO BUILD (backend only, NO UI needed):

1) Authentication & Authorization
   - Register/login for "parents" (users) via email + password, using Sanctum.
   - A parent can create/update/delete multiple "child profiles" (children) — each child belongs to exactly one user.
   - Middleware/Policy must ensure a parent can only access their own children's data (no cross-family data leaks).

2) Database schema (migrations + Eloquent models + relationships) for:
   - users (parents: id, name, email, password, timestamps)
   - children (id, user_id, name, grade [1-5], avatar, timestamps)
   - subjects (Math, Vietnamese — seed 2 records)
   - topics (id, subject_id, grade, name, description — aligned with Vietnam's national curriculum framework GDPT 2018, e.g. "Addition with carrying within 100")
   - exercises (id, topic_id, type [multiple_choice/fill_blank/...], content [JSON: question, answer, options], difficulty)
   - exercise_submissions (id, child_id, exercise_id [nullable for freeform homework], image_path [nullable], answer, is_correct, ai_feedback [JSON nullable], submitted_at)
   - ai_interactions (id, user_id, child_id, type [homework_check/qa_chat], input_summary, ai_response, model_used, input_tokens, output_tokens, estimated_cost_usd, status [success/failed], latency_ms, created_at) — used to log EVERY Claude API call
   - plans (id, name, price, monthly_ai_quota — max AI calls per month)
   - subscriptions (id, user_id, plan_id, status, current_period_end...) — keep simple, no real Stripe integration yet, just have the data structure ready for later integration
   - child_topic_mastery (id, child_id, topic_id, mastery_score) — tracks mastery level per topic

3) API Endpoints (RESTful, versioned under /api/v1/...):
   - Auth: register, login, logout, me
   - Children: CRUD (scoped to the authenticated user only)
   - Subjects & Topics: list (public, no auth required to browse content)
   - Exercises: list by topic, submit answer (auto-grade if multiple_choice/fill_blank)
   - AI Homework Check: endpoint that accepts a homework photo (multipart upload), validates/compresses image, calls Anthropic Claude API (model configurable via .env, default: claude-3-7-sonnet-20250219 or claude-3-5-sonnet-20241022, using vision with 60s timeout) to grade it, LOGS the call into ai_interactions, and returns a structured JSON result (e.g. { overall_feedback, items: [{question, is_correct, explanation}] })
   - AI Q&A Chat: endpoint that accepts a text question (already converted from speech on the frontend), calls Claude API, logs it, returns a friendly text answer tailored for primary students
   - Rate limiting: Throttle AI endpoints (e.g. max 5 requests/minute per child) to prevent accidental spam
   - BEFORE every AI call: check the user's remaining subscription/quota (count ai_interactions within the current billing cycle vs plan.monthly_ai_quota); if exhausted, return a clear 402/429 error with an appropriate message
   - Progress: endpoint returning a child's overall progress (by topic, over time)

4) Anthropic Claude API integration:
   - Create a Service class (e.g. AnthropicService or ClaudeService) that encapsulates the API call using Laravel Http client with 60s timeout (`Http::timeout(60)`). DO NOT hardcode the API key or model — read from .env (`ANTHROPIC_API_KEY`, `ANTHROPIC_MODEL=claude-3-7-sonnet-20250219`)
   - The service must support sending an image (base64) with text, and text-only requests
   - System prompt must guide Claude on: primary school context, Vietnamese language diacritics, handwriting tolerance, and positive "Sandwich" pedagogical feedback
   - Parse the response into the exact JSON structure the controller needs (use structured output by instructing Claude to return JSON in the system prompt, then parse + validate; handle JSON parse failures gracefully)
   - Record input_tokens/output_tokens from response.usage, latency_ms, and calculate estimated_cost_usd into ai_interactions for cost tracking

5) Validation & Error handling:
   - Dedicated Form Request per endpoint (RegisterRequest, HomeworkCheckRequest, etc.)
   - Consistent JSON error format: { message, errors } for 422 errors; handle Claude API failures (timeout, rate limit) separately — return clear errors without leaking technical details to the client

6) Seeders:
   - Seed 2 subjects (Math, Vietnamese)
   - Seed a few sample topics for grades 1-2 (based on the GDPT 2018 curriculum framework, DO NOT copy content from any specific textbook)
   - Seed 2-3 sample plans (Free, Family Basic, Family Premium) with different monthly_ai_quota values

7) Testing:
   - Feature tests for: register/login, creating children (and verify that user A cannot view/edit user B's children), submitting a multiple-choice exercise, and AI quota enforcement (simulate exhausted quota and confirm the AI call is blocked)

OUT OF SCOPE FOR THIS STEP:
- No frontend/UI work.
- No real Stripe/MoMo integration — just have the subscriptions data structure ready for later.
- No audio/speech processing on the backend (the frontend handles this via the Web Speech API; the backend only receives/returns text).

Start by proposing the folder structure and implementation order (migrations → models → services → controllers → routes → tests), then implement each part.
```

---

## 7. Gợi ý bước tiếp theo sau khi backend xong
- Viết prompt riêng cho **frontend** (sau khi đã chốt framework — Blade+Alpine, Vue/Inertia, hay SPA riêng).
- Viết prompt tích hợp **Stripe/thanh toán thật**.
- Viết prompt cho **admin panel** để quản lý nội dung bài học (Filament là lựa chọn tốt cho Laravel).

