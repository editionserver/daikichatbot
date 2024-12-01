<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\CustomResponse;

class OpenAIService
{
    protected $client;
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('openai.api_key');
        $this->model = config('openai.model');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function generateResponse($message)
    {
        // Önce özel yanıtları kontrol et
        $customResponse = $this->checkCustomResponse($message);
        if ($customResponse) {
            return $customResponse;
        }

        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Sen Daikin klima sistemleri konusunda uzman bir asistansın. '.
                                       'Teknik bilgileri doğru ve anlaşılır şekilde açıklarsın. '.
                                       'Yanıtların kısa ve öz olmalı, ancak gerekli tüm teknik detayları içermeli.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => config('openai.max_tokens'),
                    'temperature' => config('openai.temperature'),
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['choices'][0]['message']['content'];

        } catch (\Exception $e) {
            \Log::error('OpenAI API Hatası: ' . $e->getMessage());
            throw new \Exception('Yanıt oluşturulurken bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
        }
    }

    protected function checkCustomResponse($message)
    {
        // Mesajdaki anahtar kelimeleri kontrol et
        $keywords = CustomResponse::pluck('keyword')->toArray();
        
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                $response = CustomResponse::where('keyword', $keyword)->first();
                if ($response) {
                    return [
                        'text' => $response->response_text,
                        'attachments' => $response->attachments
                    ];
                }
            }
        }

        return null;
    }

    public function moderateContent($text)
    {
        try {
            $response = $this->client->post('/moderations', [
                'json' => [
                    'input' => $text
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['results'][0]['flagged'];

        } catch (\Exception $e) {
            \Log::warning('İçerik moderasyon hatası: ' . $e->getMessage());
            return false; // Hata durumunda içeriği engelleme
        }
    }

    public function analyzeIntent($message)
    {
        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Kullanıcının mesajındaki ana niyeti analiz et ve şu kategorilerden birine yerleştir: '.
                                       'technical_info, troubleshooting, purchase_inquiry, maintenance, general'
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 50,
                    'temperature' => 0.3,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['choices'][0]['message']['content'];

        } catch (\Exception $e) {
            \Log::error('Niyet analizi hatası: ' . $e->getMessage());
            return 'general';
        }
    }

    public function expandKnowledge($message)
    {
        // Eğer chatbot yeterli bilgiye sahip değilse, bilgi tabanını genişlet
        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Daikin klima sistemleri hakkında verilen soruyu analiz et ve '.
                                       'yanıt için gerekli olan teknik bilgileri listele.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.5,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['choices'][0]['message']['content'];

        } catch (\Exception $e) {
            \Log::error('Bilgi genişletme hatası: ' . $e->getMessage());
            return null;
        }
    }
}