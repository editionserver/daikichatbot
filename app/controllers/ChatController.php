<?php

namespace App\Controllers;

use App\Services\OpenAIService;
use App\Services\ChatService;
use App\Models\Chat;
use App\Models\Message;
use App\Models\CustomResponse;

class ChatController extends BaseController
{
    protected $openAIService;
    protected $chatService;

    public function __construct()
    {
        $this->openAIService = new OpenAIService();
        $this->chatService = new ChatService();
    }

    public function newChat()
    {
        $user = auth()->user();
        
        if (!$user && $this->chatService->getGuestMessageCount() >= config('chat.guest_limit')) {
            return response()->json([
                'error' => 'Ücretsiz deneme süreniz doldu. Devam etmek için lütfen kayıt olun.'
            ], 403);
        }

        $chat = new Chat();
        if ($user) {
            $chat->user_id = $user->id;
        }
        $chat->save();

        return response()->json([
            'chat_id' => $chat->id,
            'message' => 'Yeni sohbet başlatıldı'
        ]);
    }

    public function sendMessage()
    {
        $request = request();
        $chatId = $request->input('chat_id');
        $message = $request->input('message');

        // Özel yanıt kontrolü
        $customResponse = CustomResponse::findByKeyword($message);
        if ($customResponse) {
            return $this->handleCustomResponse($customResponse, $chatId);
        }

        // OpenAI API'ye istek
        $prompt = $this->buildPrompt($message);
        $response = $this->openAIService->generateResponse($prompt);

        // Yanıtı kaydet
        $this->chatService->saveMessage($chatId, $message, false);
        $this->chatService->saveMessage($chatId, $response, true);

        return response()->json([
            'response' => $response,
            'attachments' => null
        ]);
    }

    protected function handleCustomResponse($customResponse, $chatId)
    {
        $this->chatService->saveMessage($chatId, $customResponse->response_text, true);

        return response()->json([
            'response' => $customResponse->response_text,
            'attachments' => $customResponse->attachments
        ]);
    }

    protected function buildPrompt($message)
    {
        return "Daikin klima sistemleri uzmanı olarak yanıtla: " . $message;
    }

    public function exportChat($chatId, $format = 'pdf')
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Yetkisiz erişim'], 401);
        }

        $chat = Chat::where('user_id', $user->id)
                   ->where('id', $chatId)
                   ->firstOrFail();

        return $this->chatService->exportChat($chat, $format);
    }

    public function deleteChat($chatId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Yetkisiz erişim'], 401);
        }

        $chat = Chat::where('user_id', $user->id)
                   ->where('id', $chatId)
                   ->firstOrFail();

        $chat->delete();

        return response()->json([
            'message' => 'Sohbet başarıyla silindi'
        ]);
    }
}