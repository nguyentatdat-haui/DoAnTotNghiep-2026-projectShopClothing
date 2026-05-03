<?php

namespace App\Controllers;

use CommonHelper;

class ChatBotController extends BaseController
{
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

            // 1. Lấy thông tin từ Database
            $context = $this->getStoreContext();

            // 2. Gọi LM Studio (Local AI)
            return $this->chatWithLMStudio($message, $context);

        } catch (\Throwable $e) {
            return $this->json(['reply' => 'Hệ thống trợ lý ảo đang bảo trì. ' . $e->getMessage()]);
        }
    }

    private function getStoreContext()
    {
        try {
            $db = \Database::getInstance();
            $products = $db->fetchAll("SELECT name, base_price, discount_price FROM products ORDER BY id DESC LIMIT 30");
            $categories = $db->fetchAll("SELECT name FROM categories");

            $catNames = array_column($categories, 'name');
            $prodLines = [];
            foreach ($products as $p) {
                $base = (float)$p['base_price'];
                $discount = (!empty($p['discount_price']) && $p['discount_price'] > 0) ? (float)$p['discount_price'] : null;
                $priceStr = number_format($base) . "đ";
                if ($discount && $base > 0) {
                    $percent = round((($base - $discount) / $base) * 100);
                    $priceStr = number_format($discount) . "đ (Gốc: " . number_format($base) . "đ) [GIẢM $percent%]";
                }
                $prodLines[] = "- {$p['name']}: {$priceStr}";
            }

            $context = "DANH MỤC: " . implode(", ", $catNames) . "\n";
            $context .= "SẢN PHẨM HIỆN CÓ TRONG KHO:\n" . (empty($prodLines) ? "(Trống)" : implode("\n", $prodLines));
            return $context;
        } catch (\Throwable $e) {
            return "Lỗi dữ liệu: " . $e->getMessage();
        }
    }

    private function chatWithLMStudio($message, $context)
    {
        $apiUrl = \Config::get('LM_STUDIO_API_URL', 'http://localhost:1234/v1/chat/completions');
        $model = \Config::get('LM_STUDIO_MODEL', 'local-model');

        $systemPrompt = "Bạn là Trợ lý ảo của ClothingShop. Trả lời ngắn gọn, thân thiện bằng tiếng Việt.\n\nTHÔNG TIN CỬA HÀNG:\n" . $context;

        $data = [
            "model" => $model,
            "messages" => [
                ["role" => "system", "content" => $systemPrompt],
                ["role" => "user", "content" => $message]
            ],
            "temperature" => 0.7,
            "max_tokens" => 500
        ];

        $headers = ['Content-Type: application/json', 'Accept: application/json'];

        $result = \CommonHelper::execute_curl_request($apiUrl, $data, $headers, 'POST');
        
        if ($result['error']) {
            return $this->json(['reply' => 'Không thể kết nối với LM Studio. Lỗi: ' . $result['error']]);
        }

        $responseBody = $result['response'];
        $response = json_decode($responseBody, true);
        
        if (isset($response['choices'][0]['message']['content'])) {
            $botMessage = $response['choices'][0]['message']['content'];
        } else {
            // Log for debugging if format is unexpected
            if (\Config::bool('APP_DEBUG')) {
                return $this->json(['reply' => 'LM Studio phản hồi định dạng lạ. Nội dung: ' . substr($responseBody, 0, 200)]);
            }
            $botMessage = 'LM Studio không phản hồi đúng định dạng OpenAI.';
        }

        return $this->json(['reply' => $botMessage]);
    }
}
