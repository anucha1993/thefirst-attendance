# Employee Excel Import Guide

## คู่มือการนำเข้าข้อมูลพนักงานผ่าน Excel

### วิธีการใช้งาน

1. **ดาวน์โหลดไฟล์ตัวอย่าง**
   - เข้าไปที่หน้า Employee Management
   - คลิกปุ่ม "Import Excel"
   - คลิก "ดาวน์โหลดไฟล์ตัวอย่าง" เพื่อรับไฟล์ Template

2. **เตรียมข้อมูล**
   - เปิดไฟล์ Template ด้วย Microsoft Excel หรือ Google Sheets
   - กรอกข้อมูลพนักงานตามคอลัมน์ที่กำหนด

3. **นำเข้าข้อมูล**
   - เลือกไฟล์ที่เตรียมไว้
   - คลิก "นำเข้าข้อมูล"
   - ระบบจะตรวจสอบและบันทึกข้อมูล

---

## รูปแบบไฟล์

### คอลัมน์ที่รองรับ

| Column Name      | Required | Description                      | Example            |
|------------------|----------|----------------------------------|--------------------|
| employee_code    | ✅ Yes   | รหัสพนักงาน (ไม่ซ้ำ)              | EMP001             |
| first_name       | ✅ Yes   | ชื่อ                             | สมชาย              |
| last_name        | ✅ Yes   | นามสกุล                          | ใจดี               |
| position         | ❌ No    | ตำแหน่ง                          | พนักงานขับรถ        |
| department       | ❌ No    | แผนก                             | ฝ่ายขนส่ง          |
| phone            | ❌ No    | เบอร์โทรศัพท์                    | 081-234-5678       |
| email            | ❌ No    | อีเมล                            | somchai@example.com|
| qrcode_token     | ❌ No    | QR Code Token (ปล่อยว่างได้)     | (auto-generated)   |
| is_active        | ❌ No    | สถานะ (1=active, 0=inactive)     | 1                  |

---

## กฎการตรวจสอบข้อมูล

### ข้อมูลที่จำเป็น (Required)
- `employee_code`: ต้องไม่ซ้ำกับในระบบ
- `first_name`: ต้องกรอก (สูงสุด 255 ตัวอักษร)
- `last_name`: ต้องกรอก (สูงสุด 255 ตัวอักษร)

### ข้อมูลทางเลือก (Optional)
- `position`: ตำแหน่งงาน (สูงสุด 255 ตัวอักษร)
- `department`: แผนก (สูงสุด 255 ตัวอักษร)
- `phone`: เบอร์โทรศัพท์ (สูงสุด 20 ตัวอักษร)
- `email`: อีเมล (ต้องเป็นรูปแบบอีเมลที่ถูกต้อง)
- `qrcode_token`: ถ้าไม่กรอก ระบบจะสร้างให้อัตโนมัติ
- `is_active`: ถ้าไม่กรอก ค่าเริ่มต้นคือ 1 (active)

---

## ตัวอย่างข้อมูลใน Excel

```
| employee_code | first_name | last_name | position      | department | phone         | email               | qrcode_token | is_active |
|---------------|------------|-----------|---------------|------------|---------------|---------------------|--------------|-----------|
| EMP001        | สมชาย      | ใจดี      | พนักงานขับรถ   | ฝ่ายขนส่ง   | 081-234-5678  | somchai@example.com |              | 1         |
| EMP002        | สมหญิง     | รักดี     | พนักงานทั่วไป  | ฝ่ายผลิต   | 082-345-6789  | somying@example.com |              | 1         |
| EMP003        | สมศักดิ์   | มีสุข     | หัวหน้างาน    | ฝ่ายบริหาร  | 083-456-7890  | somsak@example.com  |              | 1         |
```

---

## ข้อจำกัด

- **ขนาดไฟล์**: สูงสุด 10MB
- **ประเภทไฟล์**: .xlsx, .xls, .csv
- **จำนวนแถว**: แนะนำไม่เกิน 5,000 แถวต่อครั้ง

---

## การแก้ไขข้อผิดพลาด

### ข้อผิดพลาดที่พบบ่อย

1. **"employee_code ต้องไม่ซ้ำ"**
   - แก้ไข: ตรวจสอบรหัสพนักงานในไฟล์ให้ไม่ซ้ำกัน และไม่ซ้ำกับในระบบ

2. **"first_name ต้องกรอก"**
   - แก้ไข: กรอกชื่อในทุกแถว

3. **"email ต้องเป็นรูปแบบอีเมลที่ถูกต้อง"**
   - แก้ไข: ตรวจสอบรูปแบบอีเมล เช่น user@example.com

4. **"ไฟล์ต้องเป็น Excel (.xlsx, .xls) หรือ CSV เท่านั้น"**
   - แก้ไข: บันทึกไฟล์เป็นรูปแบบที่รองรับ

---

## คุณสมบัติพิเศษ

### Auto QR Code Generation
- ถ้าไม่กรอก `qrcode_token` ระบบจะสร้าง QR Code Token ให้อัตโนมัติ
- Token จะมีความยาว 32 ตัวอักษร และไม่ซ้ำกัน

### Batch Processing
- สามารถนำเข้าข้อมูลพนักงานหลายคนพร้อมกันได้
- ระบบจะตรวจสอบข้อมูลทีละแถว
- หากพบข้อผิดพลาด ระบบจะแสดงรายละเอียดว่าแถวไหนผิด

---

## ขั้นตอนการนำเข้าข้อมูล 1,000+ รายการ

1. **แบ่งข้อมูลเป็นชุด** (แนะนำ 500-1000 แถวต่อไฟล์)
2. **ตรวจสอบความถูกต้องของข้อมูลก่อนนำเข้า**
   - employee_code ไม่ซ้ำ
   - มีข้อมูล first_name, last_name ครบ
3. **นำเข้าทีละไฟล์**
4. **ตรวจสอบผลลัพธ์หลังนำเข้า**

---

## Technical Details

### Import Class
- Location: `app/Imports/EmployeesImport.php`
- Uses: `maatwebsite/excel` package v3.1
- Features:
  - `ToModel` - converts rows to Employee model
  - `WithHeadingRow` - uses first row as column headers
  - `WithValidation` - validates each row

### Validation Rules
```php
[
    'employee_code' => 'required|unique:employees,employee_code',
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'position' => 'nullable|string|max:255',
    'department' => 'nullable|string|max:255',
    'phone' => 'nullable|string|max:20',
    'email' => 'nullable|email',
    'is_active' => 'nullable|boolean',
]
```

### Error Messages
- Thai language messages for better UX
- Row-by-row error reporting
- Shows exactly which field failed validation

---

## Support

หากพบปัญหาหรือมีคำถาม:
1. ตรวจสอบรูปแบบไฟล์ว่าตรงตาม Template หรือไม่
2. ตรวจสอบข้อมูลว่าครบถ้วนและถูกต้องหรือไม่
3. ลองนำเข้าข้อมูลทีละน้อยก่อน (10-20 แถว) เพื่อทดสอบ
