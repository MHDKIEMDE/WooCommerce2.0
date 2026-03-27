<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private string $fcmUrl = 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send';

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()->pluck('token')->filter()->values();

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {
            $this->send($token, $title, $body, $data);
        }
    }

    public function send(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $projectId   = config('services.fcm.project_id');
        $accessToken = $this->getAccessToken();

        if (! $projectId || ! $accessToken) {
            Log::warning('FCM non configuré — notification ignorée', compact('title', 'body'));
            return false;
        }

        $url = str_replace('{project_id}', $projectId, $this->fcmUrl);

        $payload = [
            'message' => [
                'token'        => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => ['sound' => 'default'],
                ],
                'apns' => [
                    'payload' => ['aps' => ['sound' => 'default']],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::error('FCM send failed', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'token'   => substr($fcmToken, 0, 20) . '...',
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function getAccessToken(): ?string
    {
        // En production : utiliser google/apiclient ou kreait/firebase-php
        // pour obtenir un OAuth2 access token depuis le service account JSON.
        // En développement : retourner null pour désactiver les push.
        $credentialsPath = config('services.fcm.credentials_path');

        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            return null;
        }

        try {
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($credentialsPath), true)
            );
            return $credentials->fetchAuthToken()['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('FCM access token error', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
