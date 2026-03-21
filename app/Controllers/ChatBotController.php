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

        // Prepare data for LM Studio
        $data = [
            "model" => "local-model", // LM Studio typically ignores this or uses the loaded model
            "messages" => [
                ["role" => "system", "content" => "Bạn là một trợ lý bán hàng thân thiện của cửa hàng quần áo ClothingShop. Hãy trả lời ngắn gọn, lịch sự."],
                ["role" => "user", "content" => $message]
            ],
            "temperature" => 0.7
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
