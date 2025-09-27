<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WablasWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        if (!isset($data['message'])) {
            return response()->json(['status' => 'no_message'], 400);
        }

        $sender = $data['phone']; 
        $message = strtolower(trim($data['message']));

        // Token & secret key dari Wablas
        $token = env('WABLAS_TOKEN');
        $secretKey = env('WABLAS_SECRET');

        // Default reply
        $reply = "Halo, terima kasih sudah menghubungi kami. 😊\nKetik *menu* untuk bantuan.";

        if ($message === "menu") {
            $reply = "📌 Menu Bot:\n1. Produk\n2. Cek Pesanan\n3. Bantuan";
        } elseif ($message === "produk") {
            $reply = "📦 Daftar Produk:\n- Tepung Terigu Rp15.000\n- Minyak Goreng Rp20.000\n- Gula Pasir Rp12.000";
        } elseif ($message === "halo") {
            $reply = "Halo juga! Apa kabar? 🙌";
        }

        // Kirim balasan ke API Wablas
        $url = env('WABLAS_URL');
        $response = Http::withoutVerifying()->get($url, [
            'token' => $token . '.' . $secretKey,
            'phone' => $sender,
            'message' => $reply
        ]);

        // Log untuk debugging
        Log::info("Wablas Webhook", [
            'sender' => $sender,
            'message' => $message,
            'reply' => $reply,
            'api_result' => $response->body(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
