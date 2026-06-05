<?php

namespace App\Controllers;

use App\Services\ChatbotService;
use CodeIgniter\HTTP\ResponseInterface;

class ChatbotController extends BaseController
{
    protected ChatbotService $chatbotService;

    public function __construct()
    {
        $this->chatbotService = new ChatbotService();
    }

    public function send(): ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['error' => 'Méthode non autorisée'])
                ->setStatusCode(405);
        }

        $json    = $this->request->getJSON();
        $message = $json->message ?? '';

        if (empty(trim($message))) {
            return $this->response->setJSON(['error' => 'Message vide'])
                ->setStatusCode(400);
        }

        $history = $json->history ?? [];

        try {
            $reply = $this->chatbotService->chat($message, $history);
            return $this->response->setJSON(['reply' => $reply]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()])
                ->setStatusCode(500);
        }
    }
}
