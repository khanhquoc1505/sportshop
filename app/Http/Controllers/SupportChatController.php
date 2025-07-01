<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ChatService;
use App\Models\SanPham;
use App\Models\BoMon;
use App\Models\GioiTinh;

class SupportChatController extends Controller
{
    protected ?ChatService $chat;

    public function __construct()
    {
        // Cố gắng inject ChatService, nếu không được thì để null
        try {
            $this->chat = app(ChatService::class);
        } catch (\Throwable $e) {
            Log::warning('ChatService unavailable: '.$e->getMessage());
            $this->chat = null;
        }
    }

    public function ask(Request $req)
    {
        try {
            $req->validate(['question'=>'required|string']);
            $q   = trim($req->question);
            $low = mb_strtolower($q, 'UTF-8');

            Log::info("Ask: {$q}");

            // Fallback nếu không match bất kỳ intent nào
            $reply = "Xin lỗi. Bạn có thể miêu tả chi tiết hơn không ví dụ: “bóng chuyền”, “giá 200000”.";

            // 1. Intent: theo mã sản phẩm
            if (preg_match('/\b([A-Za-z0-9]{3,})\b/', $q, $m_code)) {
                $code    = strtoupper($m_code[1]);
                $product = SanPham::where('masanpham', $code)->first();
                if ($product) {
                    $reply = "Mã {$product->masanpham}:\n"
                           . "Tên: {$product->ten}\n"
                           . "Giá: " . number_format($product->gia_ban,0,',','.') . "₫\n"
                           . "Mô tả: {$product->mo_ta}";
                    return $this->formatResponse($reply);
                }
            }

            // 2. Intent: theo bộ môn (bóng đá, bóng rổ, cầu lông, bóng chuyền…)
            // Tìm tất cả tên bộ môn trong câu
            $allBm = BoMon::pluck('bomon');
            foreach ($allBm as $bmName) {
                if (mb_stripos($low, mb_strtolower($bmName,'UTF-8')) !== false) {
                    $bomon    = BoMon::where('bomon',$bmName)->first();
                    $products = $bomon->sanPhams()->take(10)->get();
                    if ($products->isEmpty()) {
                        $reply = "Chưa có sản phẩm nào thuộc bộ môn “{$bmName}”.";
                    } else {
                        $lines = $products->map(fn($p)=>
                            "{$p->masanpham} ({$p->ten}) - ".number_format($p->gia_ban,0,',','.') . "₫"
                        )->implode("\n");
                        $reply = "Sản phẩm thuộc bộ môn “{$bmName}”:\n" . $lines. "bạn còn thắc mắc gì không ";
                    }
                    return $this->formatResponse($reply);
                }
            }

            // 3. Intent: theo giá
            // giá đúng X
            if (preg_match('/\bgiá\s+([\d\.]+)\b/', $low, $m)) {
                $x = floatval(str_replace('.','',$m[1]));
                $products = SanPham::where('gia_ban', $x)->take(10)->get();
                if ($products->isEmpty()) {
                    $reply = "Không có sản phẩm có giá " . number_format($x,0,',','.') . "₫.";
                } else {
                    $lines = $products->map(fn($p)=>
                        "{$p->masanpham} ({$p->ten})"
                    )->implode("\n");
                    $reply = "Sản phẩm có giá " . number_format($x,0,',','.') . "₫:\n" . $lines. "bạn còn thắc mắc gì không ";
                }
                return $this->formatResponse($reply);
            }
            // giá dưới X hoặc tối đa X
            if (preg_match('/\b(?:dưới|tối đa)\s*([\d\.]+)\b/', $low, $m2)) {
                $max = floatval(str_replace('.','',$m2[1]));
                $products = SanPham::where('gia_ban', '<=', $max)->take(10)->get();
                if ($products->isEmpty()) {
                    $reply = "Không có sản phẩm dưới hoặc bằng " . number_format($max,0,',','.') . "₫.";
                } else {
                    $lines = $products->map(fn($p)=>
                        "{$p->masanpham} ({$p->ten}) - ".number_format($p->gia_ban,0,',','.') . "₫"
                    )->implode("\n");
                    $reply = "Sản phẩm dưới hoặc bằng " . number_format($max,0,',','.') . "₫:\n" . $lines. "bạn còn thắc mắc gì không " ;
                }
                return $this->formatResponse($reply);
            }
            // giá trên X hoặc từ X
            if (preg_match('/\b(?:trên|từ)\s*([\d\.]+)\b/', $low, $m3)) {
                $min = floatval(str_replace('.','',$m3[1]));
                $products = SanPham::where('gia_ban', '>=', $min)->take(10)->get();
                if ($products->isEmpty()) {
                    $reply = "Không có sản phẩm trên hoặc bằng " . number_format($min,0,',','.') . "₫.";
                } else {
                    $lines = $products->map(fn($p)=>
                        "{$p->masanpham} ({$p->ten}) - ".number_format($p->gia_ban,0,',','.') . "₫"
                    )->implode("\n");
                    $reply = "Sản phẩm trên hoặc bằng " . number_format($min,0,',','.') . "₫:\n" . $lines. "bạn còn thắc mắc gì không ";
                }
                return $this->formatResponse($reply);
            }
            if (preg_match('/\b(áo|quần|bộ)\b/ui', $low, $m_type)) {
                $type = $m_type[1];
                // Tìm 10 sản phẩm có tên chứa từ này
                $products = SanPham::where('ten', 'like', "%{$type}%")
                                   ->take(10)->get();
                if ($products->isEmpty()) {
                    $reply = "Hiện không tìm thấy sản phẩm nào chứa “{$type}”.";
                } else {
                    $lines = $products->map(fn($p)=>
                        "{$p->masanpham} ({$p->ten}) - ".number_format($p->gia_ban,0,',','.') . "₫"
                    )->implode("\n");
                    $reply = "Các sản phẩm liên quan tới “{$type}”:\n{$lines}\nBạn cần thêm thông tin gì không?";
                }
                return $this->formatResponse($reply);
            }
            if (preg_match('/\b(nam|nữ|nu)\b/ui', $low, $m_gender)) {
                // chuẩn hóa
                $gender = mb_strtolower($m_gender[1], 'UTF-8');
                // Lấy danh sách sản phẩm theo relation
                $products = SanPham::whereHas('gioiTinhs', function($qg) use($gender) {
                    $qg->where('gioitinh', 'like', "%{$gender}%");
                })->take(10)->get();

                if ($products->isEmpty()) {
                    $reply = "Chưa có sản phẩm nào dành cho “{$gender}”.";
                } else {
                    $lines = $products->map(fn($p)=>
                        "{$p->masanpham} ({$p->ten}) - ".number_format($p->gia_ban,0,',','.') . "₫"
                    )->implode("\n");
                    $reply = "Sản phẩm dành cho “{$gender}”:\n{$lines}\nBạn cần thêm gì không?";
                }
                return $this->formatResponse($reply);
            }


            // Nếu không trả sau 3 intent, gửi fallback
            return $this->formatResponse($reply);

        } catch (\Throwable $e) {
            Log::error('SupportChat error: '.$e->getMessage());
            return $this->formatResponse(
                'Xin lỗi, hệ thống đang bận. Vui lòng thử lại sau.'
            );
        }
    }

    /**
     * Gửi đi phản hồi JSON, kèm qua AI nếu available
     */
    protected function formatResponse(string $reply)
    {
        // Nếu có ChatService thì làm mềm văn phong
        if ($this->chat) {
            try {
                $res = $this->chat->chat([
                    ['role'=>'system','content'=>'Bạn là trợ lý bán hàng.'],
                    ['role'=>'assistant','content'=>$reply],
                    ['role'=>'user','content'=>'Vui lòng trả lời ngắn gọn, lịch sự.'],
                ]);
                $reply = $res->choices[0]->message->content;
            } catch (\Throwable $e) {
                Log::warning('OpenAI error: '.$e->getMessage());
            }
        }
        return response()->json(['reply'=>$reply], 200);
    }
    protected function html(string $html)
{
    return response()->json(['replyHtml' => $html], 200);
}

/**
 * Xây dựng HTML cards cho mảng SanPham.
 */

}
