# Dokumentasi API Order

## Endpoint: POST /api/orders

### URL
```
POST http://localhost:8000/api/orders
```

### Headers
```
Content-Type: application/json
```

### Request Body
```json
{
  "customer_name": "Budi Santoso",
  "customer_phone": "08123456789",
  "items": [
    {
      "menu_id": 1,
      "quantity": 2
    },
    {
      "menu_id": 3,
      "quantity": 1
    }
  ]
}
```

### Validasi Request
- `customer_name`: Required, string, max 255 karakter
- `customer_phone`: Required, string, max 20 karakter
- `items`: Required, array, minimal 1 item
- `items.*.menu_id`: Required, harus ada di tabel menus
- `items.*.quantity`: Required, integer, minimal 1

### Response Success (201)
```json
{
  "success": true,
  "message": "Pesanan berhasil dibuat",
  "data": {
    "kode_pesanan": "ORD-20260119-001",
    "tanggal": "2026-01-19",
    "total": 35000,
    "items_count": 2,
    "whatsapp_sent": true
  }
}
```

### Response Error (422 - Validation)
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "customer_name": ["Nama customer harus diisi"],
    "items": ["Pesanan harus ada minimal 1 item"]
  }
}
```

### Response Error (404 - Menu Not Found)
```json
{
  "success": false,
  "message": "Menu dengan ID 99 tidak ditemukan"
}
```

### Response Error (500 - Server Error)
```json
{
  "success": false,
  "message": "Terjadi kesalahan saat memproses pesanan",
  "error": "Detail error message"
}
```

## Format WhatsApp Notification

Setelah pesanan berhasil disimpan, notifikasi WhatsApp akan dikirim ke nomor penjual dengan format:

```
🔔 PESANAN BARU

Kode Pesanan: ORD-20260119-001
Nama: Budi Santoso
No. HP: 08123456789
Tanggal: 19-01-2026

📋 DETAIL PESANAN:
1. Nasi Goreng x2 = Rp 30.000
2. Es Teh x1 = Rp 5.000

💰 TOTAL: Rp 35.000
```

## Konfigurasi Environment

Tambahkan ke `.env`:
```env
FONNTE_TOKEN=your_actual_fonnte_token
SELLER_WA=628123456789
```

**Catatan Penting:**
- Jika notifikasi WhatsApp gagal, transaksi TETAP tersimpan ke database
- Error WhatsApp akan dicatat di log Laravel
- Admin tetap bisa melihat transaksi di halaman Transaksi
- Field `whatsapp_sent` di response menunjukkan status pengiriman WhatsApp

## Testing dengan cURL

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test User",
    "customer_phone": "08123456789",
    "items": [
      {
        "menu_id": 1,
        "quantity": 2
      }
    ]
  }'
```

## Testing dengan Postman

1. Method: POST
2. URL: `http://localhost:8000/api/orders`
3. Headers: 
   - Key: `Content-Type`
   - Value: `application/json`
4. Body (raw JSON):
```json
{
  "customer_name": "Test User",
  "customer_phone": "08123456789",
  "items": [
    {
      "menu_id": 1,
      "quantity": 2
    }
  ]
}
```
