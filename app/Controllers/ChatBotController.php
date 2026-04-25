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
            if (empty($this->groqApiKey)) {
                return $this->json(['reply' => 'Lỗi: Chưa cấu hình GROQ_API_KEY trong file .env 🔑']);
            }

            // Prepare data for Groq
            $data = [
                "model" => "llama3-8b-8192", // Sử dụng model này cho ổn định và ít bị giới hạn
                "messages" => [
                    ["role" => "system", "content" => "Bạn là Trợ lý ảo của ClothingShop.
DƯỚI ĐÂY LÀ DỮ LIỆU THỰC TẾ TRONG DATABASE CỦA CỬA HÀNG:
$context

QUY TẮC:
1. Trả lời dựa trên danh sách sản phẩm.
2. Nếu không có, báo 'Hiện tại shop chưa có mặt hàng này'.
3. Ngôn ngữ: Tiếng Việt, thân thiện."],
                    ["role" => "user", "content" => $message]
                ],
                "temperature" => 0.5,
                "max_tokens" => 500
            ];

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($this->groqApiKey)
            ];

            $result = \CommonHelper::execute_curl_request($this->groqApi, $data, $headers, 'POST');

            if ($result['error']) {
                $errorMsg = 'Hệ thống trợ lý ảo đang bảo trì. 🙏';
                if (\Config::bool('APP_DEBUG')) {
                    $errorMsg .= ' (CURL Error: ' . $result['error'] . ')';
                }
                return $this->json(['reply' => $errorMsg]);
            }

            $response = json_decode($result['response'], true);
            $httpCode = $result['http_code'];

            if ($httpCode !== 200) {
                $errorMsg = 'Hệ thống trợ lý ảo đang bảo trì. 🙏';
                if (\Config::bool('APP_DEBUG')) {
                    $groqError = $response['error']['message'] ?? ($response['error'] ?? 'Unknown Error');
                    if (is_array($groqError)) $groqError = json_encode($groqError);
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
