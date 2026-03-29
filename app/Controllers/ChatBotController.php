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
                $categories = \App\Models\Category::all();
                $products = \App\Models\Product::where('status', 'active'); // SQL schema uses 'active'
                
                // Giới hạn số lượng sản phẩm để tránh quá tải token (lấy 15 sản phẩm mới nhất chẳng hạn)
                if (is_array($products)) {
                    $products = array_slice($products, -15);
                } else {
                    $products = [];
                }

                $categoryNames = array_map(function($c) { return $c->name; }, $categories);
                $productInfo = array_map(function($p) {
                    $price = $p->base_price ? number_format((float)$p->base_price) . "đ" : "Liên hệ";
                    return "- {$p->name}: Giá {$price}" . ($p->is_new ? " (Mới)" : "") . ($p->is_best_seller ? " (Bán chạy)" : "");
                }, $products);

                $context = "DANH MỤC SẢN PHẨM: " . implode(", ", $categoryNames) . "\n";
                $context .= "DANH SÁCH SẢN PHẨM TIÊU BIỂU:\n" . implode("\n", $productInfo);
            } catch (\Throwable $e) {
                // Nếu lỗi DB thì ghi nhận lỗi
                $context = "Lưu ý: Không thể truy cập database. Lỗi: " . $e->getMessage();
            }

            // Prepare data for LM Studio
            $data = [
                "model" => "local-model",
                "messages" => [
                    ["role" => "system", "content" => "Bạn là một trợ lý bán hàng chuyên nghiệp của ClothingShop.
DƯỚI ĐÂY LÀ THÔNG TIN CỬA HÀNG HIỆN TẠI:
$context

NHIỆM VỤ:
- Hỗ trợ khách hàng về sản phẩm quần áo, tư vấn chọn size, chính sách đổi trả.
- Xử lý các yêu cầu nhanh:
  + Nếu khách nói 'Sản phẩm cửa hàng' hoặc hỏi xem sản phẩm: LIỆT KÊ Ít nhất 3-5 sản phẩm tiêu biểu kèm giá từ dữ liệu trên, và các danh mục hiện có.
  + Nếu khách nói 'Tư vấn': Mời khách cho biết đang tìm đồ nam, nữ hay có nhu cầu cụ thể nào để dễ bề tư vấn.
  + Nếu khách nói 'Các bước đặt hàng': Hướng dẫn ngắn gọn các bước: 1. Chọn sản phẩm & size -> 2. Thêm vào giỏ hàng -> 3. Điền thông tin giao hàng -> 4. Thanh toán (có hỗ trợ COD).
- Sử dụng thông tin danh mục và sản phẩm ở trên để trả lời chính xác, TUYỆT ĐỐI không bịa ra sản phẩm không có trong danh sách trên.
- Nếu khách hỏi sản phẩm không có, hãy lịch sự thông báo shop hiện chưa có hoặc gợi ý mẫu khác.

QUY TẮC NGHIÊM NGẶT:
1. CHỨC NĂNG: Chỉ được phép trả lời các câu hỏi liên quan đến ClothingShop, sản phẩm thời trang, giá cả và dịch vụ của cửa hàng.
2. CẤM: Tuyệt đối không trả lời các câu hỏi về: chính trị, tôn giáo, toán học, lập trình...
3. PHẢN HỒI: Nếu khách hỏi ngoài luồng, hãy trả lời chính xác: 'Xin lỗi, tôi chỉ có thể hỗ trợ các thông tin liên quan đến sản phẩm và dịch vụ của ClothingShop.'
4. PHONG CÁCH: Trả lời tự nhiên, thân thiện, súc tích (dưới 100 chữ), dễ đọc. Có thể dùng emoji phù hợp."],
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
