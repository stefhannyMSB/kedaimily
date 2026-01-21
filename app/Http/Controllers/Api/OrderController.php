<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Datapenjualan;
use App\Models\Menu;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Store a new order
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id_menu',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'customer_name.required' => 'Nama customer harus diisi',
            'customer_phone.required' => 'Nomor telepon harus diisi',
            'items.required' => 'Pesanan harus ada minimal 1 item',
            'items.*.menu_id.required' => 'Menu ID harus ada di setiap item',
            'items.*.menu_id.exists' => 'Menu tidak ditemukan',
            'items.*.quantity.required' => 'Jumlah harus diisi',
            'items.*.quantity.min' => 'Jumlah minimal 1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate kode pesanan unik
            $kodePesanan = $this->generateOrderCode();
            $tanggal = Carbon::now()->format('Y-m-d');
            $total = 0;
            $orderItems = [];

            // Mulai database transaction
            DB::beginTransaction();

            foreach ($request->items as $item) {
                // Ambil data menu dari database
                $menu = Menu::find($item['menu_id']);
                
                if (!$menu) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Menu dengan ID {$item['menu_id']} tidak ditemukan"
                    ], 404);
                }

                $jumlah = $item['quantity'];
                $subtotal = $menu->harga * $jumlah;
                $total += $subtotal;

                // Simpan ke tabel transaksis
                Transaksi::create([
                    'kode_pesanan' => $kodePesanan,
                    'tanggal' => $tanggal,
                    'id_menu' => $menu->id_menu,
                    'jumlah' => $jumlah,
                    'total' => $subtotal,
                ]);

                // Simpan ke tabel datapenjualans
                Datapenjualan::create([
                    'kode_pesanan' => $kodePesanan,
                    'tanggal' => $tanggal,
                    'id_menu' => $menu->id_menu,
                    'jumlah' => $jumlah,
                ]);

                // Simpan untuk notifikasi WA
                $orderItems[] = [
                    'nama_menu' => $menu->nama_menu,
                    'jumlah' => $jumlah,
                    'harga' => $menu->harga,
                    'subtotal' => $subtotal,
                ];
            }

            // Commit transaction
            DB::commit();

            // Kirim notifikasi WhatsApp (jangan rollback jika gagal)
            $waData = [
                'kode_pesanan' => $kodePesanan,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'tanggal' => Carbon::now()->format('d-m-Y'),
                'items' => $orderItems,
                'total' => $total,
            ];

            // Kirim konfirmasi ke customer
            $customerWaResult = $this->whatsappService->sendCustomerConfirmation($waData);
            
            // Kirim notifikasi ke seller (admin)
            $sellerWaResult = $this->whatsappService->sendOrderNotification($waData);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => [
                    'kode_pesanan' => $kodePesanan,
                    'tanggal' => $tanggal,
                    'total' => $total,
                    'items_count' => count($orderItems),
                    'whatsapp_customer' => $customerWaResult['success'],
                    'whatsapp_seller' => $sellerWaResult['success'],
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique order code
     * Format: ORD-YYYYMMDD-XXX
     *
     * @return string
     */
    protected function generateOrderCode()
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "ORD-{$date}-";

        // Cari kode pesanan terakhir hari ini
        $lastOrder = Transaksi::where('kode_pesanan', 'LIKE', "{$prefix}%")
            ->orderBy('kode_pesanan', 'desc')
            ->first();

        if ($lastOrder) {
            // Ambil nomor urut terakhir dan tambah 1
            $lastNumber = (int) substr($lastOrder->kode_pesanan, -3);
            $newNumber = $lastNumber + 1;
        } else {
            // Pesanan pertama hari ini
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
