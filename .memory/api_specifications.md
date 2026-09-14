# Đặc Tả API — Backend "Gia Sư Nhỏ" (Little Tutor)

Base URL: `/api/v1`
Headers chung:
- `Accept: application/json`
- `Authorization: Bearer <sanctum_token>` (dành cho các endpoint bảo vệ)

---

## 1. Authentication (`/api/v1/auth`)

### 1.1 Đăng ký phụ huynh
- **POST** `/auth/register`
- **Body**:
  ```json
  {
    "name": "Nguyen Van A",
    "email": "parent@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response** `201 Created`:
  ```json
  {
    "message": "Đăng ký thành công",
    "user": { "id": 1, "name": "Nguyen Van A", "email": "parent@example.com" },
    "token": "1|sanctum_token_string..."
  }
  ```

### 1.2 Đăng nhập
- **POST** `/auth/login`
- **Body**:
  ```json
  {
    "email": "parent@example.com",
    "password": "password123"
  }
  ```
- **Response** `200 OK`:
  ```json
  {
    "message": "Đăng nhập thành công",
    "user": { "id": 1, "name": "Nguyen Van A", "email": "parent@example.com" },
    "token": "2|sanctum_token_string..."
  }
  ```

### 1.3 Đăng xuất & Xem thông tin
- **POST** `/auth/logout` $\rightarrow$ `200 OK`
- **GET** `/auth/me` $\rightarrow$ `200 OK` (thông tin user + danh sách con + gói cước hiện tại)

---

## 2. Quản lý Hồ sơ Con (`/api/v1/children`)

Tất cả endpoint đều được bảo vệ bởi `ChildPolicy`, phụ huynh chỉ truy cập được con của mình.

- **GET** `/children`: Danh sách các bé của phụ huynh.
- **POST** `/children`: Tạo hồ sơ bé mới (`name`, `grade` [1-5], `avatar`).
- **GET** `/children/{id}`: Chi tiết hồ sơ bé.
- **PUT** `/children/{id}`: Cập nhật thông tin bé.
- **DELETE** `/children/{id}`: Xóa hồ sơ bé.

---

## 3. Môn học & Chủ đề (`/api/v1/subjects`, `/api/v1/topics`)

- **GET** `/subjects`: Danh sách môn học (Toán, Tiếng Việt) — Public.
- **GET** `/topics?subject_id=1&grade=2`: Danh sách chủ đề theo môn và khối lớp — Public.
- **GET** `/topics/{id}`: Chi tiết chủ đề.

---

## 4. Bài tập & Nộp bài (`/api/v1/exercises`)

- **GET** `/topics/{topicId}/exercises`: Danh sách bài tập trong chủ đề.
- **POST** `/exercises/{exerciseId}/submit`:
  - **Body**:
    ```json
    {
      "child_id": 1,
      "answer": "B"
    }
    ```
  - **Response** `200 OK`:
    ```json
    {
      "is_correct": true,
      "explanation": "Chính xác! 15 + 8 = 23 (5 cộng 8 bằng 13, viết 3 nhớ 1...).",
      "submission_id": 10
    }
    ```

---

## 5. Gia Sư AI (`/api/v1/ai`)

Rate Limit: 5 requests / phút / child.

### 5.1 Chấm bài qua ảnh chụp
- **POST** `/ai/homework-check`
- **Content-Type**: `multipart/form-data`
- **Fields**:
  - `child_id`: (integer) ID của bé
  - `image`: (file: jpeg, png, webp, max 10MB)
  - `note`: (string, tùy chọn, ví dụ: "Cô giáo giao bài 3 và 4 trang 25")
- **Response** `200 OK`:
  ```json
  {
    "status": "success",
    "overall_feedback": "Bé viết chữ và số rất rõ ràng, thẳng hàng! Bé đã làm đúng 3/4 câu...",
    "items": [
      {
        "question": "Bài 1: Đặt tính rồi tính 24 + 38",
        "is_correct": true,
        "explanation": "Bé đặt tính thẳng cột và tính đúng kết quả là 62."
      },
      {
        "question": "Bài 2: Điền c hay k vào chỗ trống: ...on ...ua",
        "is_correct": false,
        "explanation": "Bé điền 'k' là chưa đúng quy tắc. Quy tắc: 'k' chỉ đi với e, ê, i; còn 'c' đi với các âm còn lại. Đáp án đúng là: con cua."
      }
    ],
    "quota_remaining": 8
  }
  ```
- **Response lỗi hết quota** `402 Payment Required`:
  ```json
  {
    "message": "Bạn đã sử dụng hết lượt AI trong tháng. Vui lòng nâng cấp gói cước để tiếp tục."
  }
  ```

### 5.2 Hỏi đáp AI (Voice Q&A converted to Text)
- **POST** `/ai/qa`
- **Body**:
  ```json
  {
    "child_id": 1,
    "question": "Tại sao con cua lại đi ngang hả gia sư?"
  }
  ```
- **Response** `200 OK`:
  ```json
  {
    "status": "success",
    "answer": "Chào con! Con hỏi một câu rất thú vị đấy! Loài cua đi ngang vì các khớp chân của bạn cua uốn cong sang hai bên chứ không uốn về phía trước như chân của chúng ta...",
    "quota_remaining": 7
  }
  ```

---

## 6. Tiến độ học tập (`/api/v1/children/{childId}/progress`)

- **GET** `/children/{childId}/progress`: Trả về điểm thành thạo theo từng chủ đề, tổng số bài tập đã làm, tỷ lệ đúng, và lịch sử chấm bài gần nhất.
