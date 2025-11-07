<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsappOtpSender
{
    public function send(string $message): void
    {
        $rawRecipient = config('services.whatsapp.recipient');

        if (! $rawRecipient) {
            Log::warning('WhatsApp recipient not configured for OTP delivery.', [
                'message' => $message,
            ]);

            return;
        }

        $countryCode = (string) config('services.whatsapp.country_code', '62');
        $recipient = $this->normalizeRecipient($rawRecipient, $countryCode);

        if ($recipient === '') {
            Log::warning('WhatsApp recipient number is invalid.', [
                'raw' => $rawRecipient,
            ]);

            return;
        }

        $endpoint = config('services.whatsapp.endpoint');
        $token = config('services.whatsapp.token');

        if (! $endpoint || ! $token) {
            Log::info('WhatsApp OTP delivery skipped (missing endpoint or token).', [
                'endpoint' => $endpoint,
                'has_token' => (bool) $token,
            ]);

            return;
        }

        $payload = $this->buildPayload($recipient, $message, $countryCode);

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $token,
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->post($endpoint, $payload);

            $this->handleResponse($response, $payload);
        } catch (Throwable $exception) {
            Log::error('WhatsApp OTP delivery threw an exception.', [
                'payload' => $payload,
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function buildPayload(string $recipient, string $message, string $countryCode): array
    {
        $payload = [
            'target' => $recipient,
            'message' => $message,
        ];

        if ($device = config('services.whatsapp.device')) {
            $payload['device'] = $device;
        }

        if ($countryCode !== '') {
            $payload['countryCode'] = $countryCode;
        }

        if ($delay = config('services.whatsapp.delay')) {
            $payload['delay'] = $delay;
        }

        if (filter_var(config('services.whatsapp.typing'), FILTER_VALIDATE_BOOLEAN)) {
            $payload['typing'] = 'true';
        }

        return $payload;
    }

    private function normalizeRecipient(string $recipient, string $countryCode): string
    {
        $digits = preg_replace('/\D+/', '', $recipient) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
            $digits = $countryCode . $digits;
        }

        if (! str_starts_with($digits, $countryCode)) {
            $digits = $countryCode . ltrim($digits, '0');
        }

        return $digits;
    }

    private function handleResponse(Response $response, array $payload): void
    {
        $body = $response->body();
        $json = $response->json();

        $statusOk = is_array($json) ? ($json['status'] ?? false) === true : false;

        if ($response->failed() || ! $statusOk) {
            Log::error('WhatsApp OTP delivery failed.', [
                'payload' => $payload,
                'body' => $body,
                'decoded' => $json,
                'status_code' => $response->status(),
            ]);

            return;
        }

        Log::info('WhatsApp OTP delivered.', [
            'target' => $payload['target'],
            'response' => $json,
        ]);
    }
}
