<?php

namespace App\Services;

use Brevo\Client\Api\SendersApi;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class BrevoMailer
{
    private ?Configuration $configuration = null;
    private ?TransactionalEmailsApi $transactionalApi = null;
    private ?SendersApi $sendersApi = null;
    private ?array $senderCache = null;

    public function send(string $toEmail, ?string $toName, string $subject, string $html): void
    {
        $senderEmail = config('mail.from.address');
        $senderName = config('mail.from.name');

        if (! $senderEmail) {
            throw new RuntimeException('Alamat pengirim belum dikonfigurasi.');
        }

        $this->assertSenderVerified($senderEmail);

        $email = (new SendSmtpEmail())
            ->setSubject($subject)
            ->setHtmlContent($html)
            ->setSender([
                'email' => $senderEmail,
                'name' => $senderName,
            ])
            ->setTo([
                [
                    'email' => $toEmail,
                    'name' => $toName ?: $toEmail,
                ],
            ])
            ->setHeaders([
                'X-Mailer' => 'Monitoring Kepangkatan SDM',
            ]);

        try {
            $response = $this->transactionalEmailsApi()->sendTransacEmail($email);

            Log::info('Brevo email sent.', [
                'to' => $toEmail,
                'messageId' => $response->getMessageId(),
            ]);
        } catch (ApiException $exception) {
            Log::error('Brevo API returned error.', [
                'status' => $exception->getCode(),
                'response' => $exception->getResponseBody(),
            ]);

            throw new RuntimeException('Brevo menolak pengiriman email: ' . $exception->getMessage(), 0, $exception);
        } catch (Throwable $exception) {
            Log::error('Brevo email send threw exception.', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function assertSenderVerified(string $senderEmail): void
    {
        $senderEmail = trim(strtolower($senderEmail));

        if ($senderEmail === '') {
            throw new RuntimeException('Alamat email pengirim belum dikonfigurasi.');
        }

        $sender = $this->listSenders()
            ->first(fn (array $item) => strtolower($item['email'] ?? '') === $senderEmail);

        if (! $sender || ! ($sender['active'] ?? false)) {
            throw new RuntimeException(
                sprintf(
                    'Email pengirim %s belum diverifikasi di Brevo. Tambahkan dan aktifkan melalui menu Senders & IP.',
                    $senderEmail
                )
            );
        }
    }

    private function listSenders(): Collection
    {
        if ($this->senderCache !== null) {
            return collect($this->senderCache);
        }

        try {
            $response = $this->sendersApi()->getSenders();

            $items = collect($response->getSenders() ?? [])
                ->map(fn ($sender) => [
                    'email' => $sender->getEmail(),
                    'name' => $sender->getName(),
                    'active' => $sender->getActive(),
                ])
                ->all();

            return collect($this->senderCache = $items);
        } catch (ApiException $exception) {
            Log::error('Brevo senders API error.', [
                'status' => $exception->getCode(),
                'response' => $exception->getResponseBody(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Exception saat mengambil daftar pengirim Brevo.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return collect($this->senderCache = []);
    }

    private function transactionalEmailsApi(): TransactionalEmailsApi
    {
        if ($this->transactionalApi) {
            return $this->transactionalApi;
        }

        return $this->transactionalApi = new TransactionalEmailsApi(
            new HttpClient(),
            $this->configuration()
        );
    }

    private function sendersApi(): SendersApi
    {
        if ($this->sendersApi) {
            return $this->sendersApi;
        }

        return $this->sendersApi = new SendersApi(
            new HttpClient(),
            $this->configuration()
        );
    }

    private function configuration(): Configuration
    {
        if ($this->configuration) {
            return $this->configuration;
        }

        $apiKey = config('services.brevo.api_key');

        if (! $apiKey) {
            throw new RuntimeException('Brevo API key belum dikonfigurasi.');
        }

        $config = new Configuration();
        $config->setApiKey('api-key', $apiKey);

        return $this->configuration = $config;
    }
}
