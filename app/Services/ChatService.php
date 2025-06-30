<?php
namespace App\Services;

use OpenAI\Client;

class ChatService
{
    protected Client $openAI;

    public function __construct()
    {
        $this->openAI = Client::factory([
            'api_key' => config('services.openai.key'),
        ]);
    }

    /**
     * Gửi mảng messages (role/content) và nhận về object response
     */
    public function chat(array $messages)
    {
        return $this->openAI->chat()->create([
            'model'    => 'gpt-3.5-turbo',
            'messages' => $messages,
            'stream'   => false,
        ]);
    }
}
