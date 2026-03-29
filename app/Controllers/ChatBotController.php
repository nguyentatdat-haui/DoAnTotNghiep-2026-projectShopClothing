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
        $message = $_POST['message'] ?? '';

        if (empty($message)) {
            return $this->json(['error' => 'Message is required'], 400);
        }

        // 1. Lấy thông tin từ Database để làm ngữ cảnh (Context)
        $categories = \App\Models\Category::all();
        $products = \App\Models\Product::where('status', '1'); // Giả định status 1 là đang bán
        
        // Giới hạn số lượng sản phẩm để tránh quá tải token (lấy 15 sản phẩm mới nhất chẳng hạn)
        $products = array_slice($products, -15);

        $categoryNames = array_map(function($c) { return $c->name; }, $categories);
        $productInfo = array_map(function($p) {
            return "- {$p->name}: Giá " . number_format($p->base_price) . "đ" . ($p->is_new ? " (Mới)" : "") . ($p->is_best_seller ? " (Bán chạy)" : "");
        }, $products);

        $context = "DANH MỤC SẢN PHẨM: " . implode(", ", $categoryNames) . "\n";
        $context .= "DANH SÁCH SẢN PHẨM TIÊU BIỂU:\n" . implode("\n", $productInfo);

        // Prepare data for LM Studio
        $data = [
            "model" => "local-model",
            "messages" => [
                ["role" => "system", "content" => "Bạn là một trợ lý bán hàng chuyên nghiệp của ClothingShop.
DƯỚI ĐÂY LÀ THÔNG TIN CỬA HÀNG HIỆN TẠI:
$context

NHIỆM VỤ:
- Hỗ trợ khách hàng về sản phẩm quần áo, tư vấn chọn size, chính sách đổi trả.
- Sử dụng thông tin danh mục và sản phẩm ở trên để trả lời chính xác.
- Nếu khách hỏi sản phẩm không có trong danh sách, hãy nói shop còn nhiều mẫu khác và mời khách xem trực tiếp trên website.

QUY TẮC NGHIÊM NGẶT:
1. CHỨC NĂNG: Chỉ được phép trả lời các câu hỏi liên quan đến ClothingShop, sản phẩm thời trang, giá cả và dịch vụ của cửa hàng.
2. CẤM: Tuyệt đối không trả lời các câu hỏi về: chính trị, tôn giáo, toán học, lập trình...
3. PHẢN HỒI: Nếu khách hỏi ngoài luồn, hãy trả lời: 'Xin lỗi, tôi chỉ có thể hỗ trợ các thông tin liên quan đến sản phẩm và dịch vụ của ClothingShop.'
4. PHONG CÁCH: Trả lời ngắn gọn, lịch sự, chuyên nghiệp bằng tiếng Việt."],
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
            return $this->json([
                'error' => 'Không thể kết nối tới LM Studio. Hãy chắc chắn bạn đã bật Server trong LM Studio (cổng 1234).',
                'details' => $result['error']
            ], 500);
        }

        $response = json_decode($result['response'], true);
        $botMessage = $response['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi gặp trục trặc khi xử lý câu hỏi.';

        return $this->json([
            'reply' => $botMessage
        ]);
    }
}
