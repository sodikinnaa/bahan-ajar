<?php
class OpenAIClient {
    private $baseUrl;
    private $apiKey;
    private $model;

    public function __construct($baseUrl, $apiKey, $model = null) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    public function setModel($model) {
        $this->model = $model;
    }

    public function getModels() {
        $url = $this->baseUrl . '/v1/models';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => OPENAI_API_TIMEOUT,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to fetch models: HTTP ' . $httpCode);
        }

        $data = json_decode($response, true);
        return $data['data'] ?? [];
    }

    public function generateSlideContent($prompt, $context = []) {
        if (!$this->model) {
            throw new Exception('Model not set');
        }

        $systemPrompt = "You are an expert educational content creator for Noodu Academy. "
            . "Your task is to create high-quality, engaging slide content for educational modules. "
            . "Follow the Noodu teaching style: practical examples, clear explanations, and learner-focused design. "
            . "Structure responses as JSON with 'slides' array containing objects with 'title', 'content' (HTML), and 'notes'.";

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        if (!empty($context)) {
            $messages[0]['content'] .= "\n\nContext: " . json_encode($context);
        }

        $url = $this->baseUrl . '/v1/chat/completions';

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4000,
            'response_format' => ['type' => 'json_object']
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => OPENAI_API_TIMEOUT,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception('CURL Error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            throw new Exception('API Error (HTTP ' . $httpCode . '): ' . $response);
        }

        $data = json_decode($response, true);
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new Exception('Invalid API response format');
        }

        $content = $data['choices'][0]['message']['content'];
        return json_decode($content, true);
    }
}
?>
