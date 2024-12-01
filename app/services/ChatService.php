<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ChatService
{
    protected $openAIService;
    protected $fileService;

    public function __construct(OpenAIService $openAIService, FileService $fileService)
    {
        $this->openAIService = $openAIService;
        $this->fileService = $fileService;
    }

    public function createChat(User $user = null)
    {
        $chat = new Chat();
        if ($user) {
            $chat->user_id = $user->id;
        }
        $chat->save();

        // İlk sistem mesajını ekle
        $this->addSystemMessage($chat);

        return $chat;
    }

    public function processMessage(Chat $chat, string $message)
    {
        // Kullanıcı mesajını kaydet
        $userMessage = $this->saveMessage($chat, $message, false);

        // OpenAI'dan yanıt al
        try {
            $response = $this->openAIService->generateResponse($message);
            $botMessage = $this->saveMessage($chat, $response['text'], true);

            // Eğer yanıtta ekler varsa, onları da kaydet
            if (!empty($response['attachments'])) {
                foreach ($response['attachments'] as $attachment) {
                    $this->fileService->attachToMessage($botMessage, $attachment);
                }
            }

            return $botMessage;
        } catch (\Exception $e) {
            \Log::error('ChatService Error: ' . $e->getMessage());
            throw new \Exception('Yanıt oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    public function saveMessage(Chat $chat, string $content, bool $isBot)
    {
        return $chat->messages()->create([
            'content' => $content,
            'is_bot' => $isBot
        ]);
    }

    protected function addSystemMessage(Chat $chat)
    {
        $welcomeMessage = "Merhaba! Ben Daikin klima sistemleri uzmanıyım. Size nasıl yardımcı olabilirim?";
        return $this->saveMessage($chat, $welcomeMessage, true);
    }

    public function getGuestMessageCount()
    {
        $sessionId = session()->getId();
        return Cache::get("guest_messages_{$sessionId}", 0);
    }

    public function incrementGuestMessageCount()
    {
        $sessionId = session()->getId();
        $count = $this->getGuestMessageCount();
        Cache::put("guest_messages_{$sessionId}", $count + 1, now()->addHours(24));
    }

    public function exportChat(Chat $chat, string $format = 'txt')
    {
        switch ($format) {
            case 'txt':
                return $this->exportAsTxt($chat);
            case 'pdf':
                return $this->exportAsPdf($chat);
            default:
                throw new \InvalidArgumentException('Desteklenmeyen format');
        }
    }

    protected function exportAsTxt(Chat $chat)
    {
        $content = '';
        foreach ($chat->messages as $message) {
            $sender = $message->is_bot ? 'Bot' : 'Siz';
            $content .= "[{$message->created_at}] {$sender}:\n{$message->content}\n\n";
        }
        return $content;
    }

    protected function exportAsPdf(Chat $chat)
    {
        $pdf = \PDF::loadView('exports.chat', [
            'chat' => $chat,
            'messages' => $chat->messages
        ]);
        return $pdf->output();
    }

    public function analyzeSentiment(Chat $chat)
    {
        $messages = $chat->messages()
                        ->where('is_bot', false)
                        ->get();

        $sentiments = [];
        foreach ($messages as $message) {
            $sentiments[] = $message->getSentimentScore();
        }

        return [
            'average' => empty($sentiments) ? 0 : array_sum($sentiments) / count($sentiments),
            'trend' => $this->calculateSentimentTrend($sentiments)
        ];
    }

    protected function calculateSentimentTrend(array $sentiments)
    {
        if (count($sentiments) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($sentiments, 0, floor(count($sentiments) / 2));
        $secondHalf = array_slice($sentiments, floor(count($sentiments) / 2));

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        $difference = $secondAvg - $firstAvg;

        if ($difference > 0.2) {
            return 'improving';
        } elseif ($difference < -0.2) {
            return 'declining';
        }

        return 'stable';
    }

    public function getPopularTopics(Chat $chat)
    {
        $messages = $chat->messages()
                        ->where('is_bot', false)
                        ->get();

        $topics = [];
        foreach ($messages as $message) {
            $words = explode(' ', strtolower($message->content));
            foreach ($words as $word) {
                if (strlen($word) > 3) { // Kısa kelimeleri atla
                    $topics[$word] = ($topics[$word] ?? 0) + 1;
                }
            }
        }

        arsort($topics);
        return array_slice($topics, 0, 5);
    }
}