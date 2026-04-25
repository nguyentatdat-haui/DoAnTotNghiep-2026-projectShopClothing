<?php

namespace App\Controllers;

use CommonHelper;

class ChatBotController extends BaseController
{
    /**
     * Groq API Endpoint
     */
    private $groqApi = "https://api.groq.com/openai/v1/chat/completions";
    private $groqApiKey;

    public function __construct()
    {
        parent::__construct();
        $this->groqApiKey = \Config::get('GROQ_API_KEY');
    }

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

            // 1. Lấy thông tin từ Database bằng SQL trực tiếp để đảm bảo dữ liệu mới nhất
            $context = "";
            try {
                $db = \Database::getInstance();
                
                // Lấy danh sách sản phẩm thực tế từ DB
                $products = $db->fetchAll("SELECT name, base_price, discount_price FROM products ORDER BY id DESC LIMIT 30");
                $categories = $db->fetchAll("SELECT name FROM categories");

                $catNames = [];
                foreach ($categories as $cat) {
                    $catNames[] = $cat['name'];
                }

                $prodLines = [];
                if (!empty($products)) {
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
                }

                $context = "DANH MỤC: " . implode(", ", $catNames) . "\n";
                $context .= "SẢN PHẨM HIỆN CÓ TRONG KHO (DỮ LIỆU THỰC):\n";
                if (empty($prodLines)) {
                    $context .= "(Hiện tại không có sản phẩm nào trong database)";
                } else {
                    $context .= implode("\n", $prodLines);
                }
            } catch (\Throwable $e) {
                $context = "Lỗi truy cập dữ liệu: " . $e->getMessage();
            }

            // 2. Kiểm tra API Key
            $cleanKey = trim($this->groqApiKey);
            if (empty($cleanKey)) {
                return $this->json(['reply' => 'Lỗi: Chưa cấu hình GROQ_API_KEY trong file .env 🔑']);
            }

            if (strpos($cleanKey, 'gsk_') !== 0) {
                return $this->json(['reply' => 'Lỗi: API Key trong .env không đúng định dạng (phải bắt đầu bằng gsk_). Hiện tại: ' . substr($cleanKey, 0, 5) . '...']);
            }

            // Prepare data for Groq
            $data = [
                "model" => "llama-3.1-8b-instant", // Model này rất nhẹ và ổn định
                "messages" => [
                    ["role" => "system", "content" => "Bạn là Trợ lý ảo của ClothingShop. Trả lời ngắn gọn, thân thiện bằng tiếng Việt."],
                    ["role" => "user", "content" => $message]
                ],
                "temperature" => 0.5,
                "max_tokens" => 500
            ];

            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: ClothingShop-ChatBot/1.1',
                'Authorization: Bearer ' . $cleanKey
            ];

            $result = \CommonHelper::execute_curl_request($this->groqApi, $data, $headers, 'POST');

            if ($result['error']) {
                $errorMsg = 'Hệ thống trợ lý ảo đang bảo trì. 🙏';
                if (\Config::bool('APP_DEBUG')) {
                    $errorMsg .= ' (CURL Error: ' . $result['error'] . ')';
                }
                return $this->json(['reply' => $errorMsg]);
            }

            $responseBody = $result['response'];
            $response = json_decode($responseBody, true);
            $httpCode = $result['http_code'];

            if ($httpCode !== 200) {
                $errorMsg = 'Hệ thống trợ lý ảo đang bảo trì. 🙏';
                if (\Config::bool('APP_DEBUG')) {
                    if ($response && isset($response['error'])) {
                        $groqError = $response['error']['message'] ?? json_encode($response['error']);
                    } else {
                        // Nếu không phải JSON (có thể là trang HTML 403 của Cloudflare)
                        $groqError = substr(strip_tags($responseBody), 0, 100); 
                    }
                    $errorMsg .= " (Groq Error $httpCode: $groqError)";
                }
                return $this->json(['reply' => $errorMsg]);
            }

            $botMessage = $response['choices'][0]['message']['content'] ?? 'Tôi đang bận một chút, bạn thử lại sau nhen!';

            return $this->json(['reply' => $botMessage]);
        } catch (\Throwable $e) {
            return $this->json(['reply' => 'Hệ thống trợ lý ảo đang bảo trì.']);
        }
    }
}
