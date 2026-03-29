<?php

namespace App\Controllers;

use CommonHelper;

class ChatBotController extends BaseController
{
    /**
     * LM Studio API Endpoint (Default)
     */
    private $lmStudioApi = "http://localhost:1234/v1/chat/completions";

    public function index()
    {
        return $this->view('chatbot/index', [
            'title' => 'Cửa hàng quần áo - ChatBot AI'
        ]);
    }

    public function chat()
    {
        try {
            $message = $_POST['message'] ?? '';

            if (empty($message)) {
                return $this->json(['error' => 'Message is required'], 400);
            }

            // 1. Lấy thông tin từ Database để làm ngữ cảnh (Context)
            $context = "";
            try {
                $categories = \App\Models\Category::all() ?: [];
                $products = \App\Models\Product::all() ?: [];
                
                // Giới hạn số lượng sản phẩm để tránh quá tải token (lấy 15 sản phẩm mới nhất)
                if (is_array($products)) {
                    $products = array_slice($products, -15);
                } else {
                    $products = [];
                }

                $categoryNames = array_map(function($c) { return $c->name; }, $categories);
                $productInfo = array_map(function($p) {
                    // Ưu tiên giá khuyến mãi nếu có
                    $finalPrice = (!empty($p->discount_price) && $p->discount_price > 0) ? $p->discount_price : $p->base_price;
                    $price = $finalPrice ? number_format((float)$finalPrice) . "đ" : "Liên hệ";
                    return "- {$p->name} (Giá: {$price})";
                }, $products);

                if (empty($productInfo)) {
                    $productInfo[] = "(Hiện tại shop đang cập nhật sản phẩm, chưa có mặt hàng nào)";
                }

                $context = "DANH MỤC SẢN PHẨM: " . implode(", ", $categoryNames) . "\n";
                $context .= "DANH SÁCH SẢN PHẨM Ở SHOP:\n" . implode("\n", $productInfo);
            } catch (\Throwable $e) {
                // Nếu lỗi DB thì ghi nhận lỗi
                $context = "Lưu ý: Không thể truy cập database. Lỗi: " . $e->getMessage();
            }

            // Prepare data for LM Studio
            $data = [
                "model" => "local-model",
                "messages" => [
                    ["role" => "system", "content" => "Bạn là một trợ lý bán hàng chuyên nghiệp của ClothingShop.
DƯỚI ĐÂY LÀ THÔNG TIN CỬA HÀNG HIỆN TẠI (CHỈ BÁN NHỮNG MÓN NÀY):
$context

NHIỆM VỤ:
- Hỗ trợ khách hàng về sản phẩm quần áo, tư vấn chọn size, chính sách đổi trả.
- Xử lý các yêu cầu nhanh:
  + Nếu khách nói 'Sản phẩm cửa hàng' hoặc hỏi sản phẩm: CHỈ ĐƯỢC LIỆT KÊ các sản phẩm có thật trong phần [DANH SÁCH SẢN PHẨM Ở SHOP] bên trên. TUYỆT ĐỐI KHÔNG tự bịa ra hay sáng tác thêm sản phẩm hoặc giá ảo. Nếu danh sách chỉ có 2 sản phẩm, chỉ liệt kê đúng 2 sản phẩm đó.
  + Nếu khách nói 'Tư vấn': Mời khách cho biết đang tìm đồ nam, nữ hay có nhu cầu cụ thể nào để dễ bề tư vấn.
  + Nếu khách nói 'Các bước đặt hàng': Hướng dẫn ngắn gọn các bước: Chọn sản phẩm -> Thêm vào giỏ -> Điền thông tin -> Đặt hàng.
- Trả lời ngắn gọn, lịch sự, thân thiện bằng tiếng Việt. Dưới 100 chữ."],
                    ["role" => "user", "content" => $message]
                ],
                "temperature" => 0.4
            ];

            $headers = [
                'Content-Type: application/json'
            ];

            // Use the existing helper for cURL
            $result = CommonHelper::execute_curl_request($this->lmStudioApi, $data, $headers, 'POST');

            if ($result['error']) {
                $errorMsg = 'Không thể kết nối tới LM Studio (' . $this->lmStudioApi . '). Hãy chắc chắn Cổng 1234 đang mở.';
                if (isset($result['error'])) {
                    $errorMsg .= ' Chi tiết: ' . $result['error'];
                }
                if (isset($result['http_code']) && $result['http_code'] > 0) {
                    $errorMsg .= ' Mã HTTP: ' . $result['http_code'];
                }
                return $this->json([
                    'error' => $errorMsg,
                    'details' => $result['error']
                ], 500);
            }

            $response = json_decode($result['response'], true);
            $botMessage = $response['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi gặp trục trặc khi xử lý câu hỏi. Response: ' . $result['response'];

            return $this->json([
                'reply' => $botMessage
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Lỗi hệ thống (PHP): ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine()
            ], 500);
        }
    }
}
