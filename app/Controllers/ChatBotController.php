<?php

namespace App\Controllers;

use CommonHelper;

class ChatBotController extends BaseController
{
    /**
     * Gemini API Endpoint
     */
    private $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent";
    private $geminiApiKey;

    public function __construct()
    {
        parent::__construct();
        $this->geminiApiKey = \Config::get('GEMINI_API_KEY');
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
            $cleanKey = trim($this->geminiApiKey);
            if (empty($cleanKey)) {
                return $this->json(['reply' => 'Lỗi: Chưa cấu hình GEMINI_API_KEY trong file .env 🔑']);
            }

            // Prepare data for Gemini
            $fullPrompt = "Bạn là Trợ lý ảo của ClothingShop. Trả lời ngắn gọn, thân thiện bằng tiếng Việt.\n\n";
            $fullPrompt .= "THÔNG TIN CỬA HÀNG:\n" . $context . "\n\n";
            $fullPrompt .= "Khách hàng hỏi: " . $message;

            $data = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $fullPrompt]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "temperature" => 0.5,
                    "maxOutputTokens" => 500
                ]
            ];

            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: ClothingShop-ChatBot/1.1'
            ];

            $endpoint = $this->geminiApiUrl . '?key=' . $cleanKey;

            $result = \CommonHelper::execute_curl_request($endpoint, $data, $headers, 'POST');

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
                        $geminiError = $response['error']['message'] ?? json_encode($response['error']);
                    } else {
                        // Nếu không phải JSON
                        $geminiError = substr(strip_tags($responseBody), 0, 100); 
                    }
                    $errorMsg .= " (Gemini Error $httpCode: $geminiError)";
                }
                return $this->json(['reply' => $errorMsg]);
            }

            $botMessage = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Tôi đang bận một chút, bạn thử lại sau nhen!';

            return $this->json(['reply' => $botMessage]);
        } catch (\Throwable $e) {
            return $this->json(['reply' => 'Hệ thống trợ lý ảo đang bảo trì.']);
        }
    }
}
