<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private string $apiToken;
    private string $phoneNumberId;
    private string $defaultCountryCode;
    private bool $enabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com');
        $this->apiToken = config('services.whatsapp.api_token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
        $this->defaultCountryCode = config('services.whatsapp.default_country_code', '91');
        $this->enabled = config('services.whatsapp.enabled', false);
    }

    /**
     * Send a text message via WhatsApp
     * Note: Free-form messages only work within 24-hour window after user's last message
     * For outbound messages, use sendTemplateMessage() instead
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->enabled || empty($this->apiToken) || empty($this->phoneNumberId)) {
            Log::warning('WhatsApp service is not properly configured or disabled');
            return false;
        }

        $phoneNumber = $this->formatPhoneNumber($to);

        $url = "{$this->apiUrl}/v18.0/{$this->phoneNumberId}/messages";

        try {
            $response = Http::withToken($this->apiToken)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $phoneNumber,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            $responseData = $response->json();

            if ($response->successful()) {
                // Check if there are any errors in the response
                if (isset($responseData['error'])) {
                    Log::error('WhatsApp API returned error', [
                        'to' => $phoneNumber,
                        'error' => $responseData['error'],
                        'error_type' => $responseData['error']['type'] ?? null,
                        'error_code' => $responseData['error']['code'] ?? null,
                        'error_message' => $responseData['error']['message'] ?? null,
                        'error_subcode' => $responseData['error']['error_subcode'] ?? null,
                    ]);
                    return false;
                }

                $messageId = $responseData['messages'][0]['id'] ?? null;
                
                // Check if wa_id is different from input (number was corrected)
                $inputNumber = $responseData['contacts'][0]['input'] ?? null;
                $waId = $responseData['contacts'][0]['wa_id'] ?? null;
                
                if ($inputNumber !== $waId) {
                    Log::warning('WhatsApp corrected phone number format', [
                        'input' => $inputNumber,
                        'corrected_to' => $waId,
                    ]);
                }

                Log::info('WhatsApp message accepted by API', [
                    'to' => $phoneNumber,
                    'wa_id' => $waId,
                    'message_id' => $messageId,
                    'response' => $responseData,
                    'note' => 'Message accepted but delivery depends on 24-hour window. Check webhook for delivery status.',
                ]);
                
                return true;
            }

            Log::error('Failed to send WhatsApp message', [
                'to' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->body(),
                'error' => $responseData['error'] ?? null,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp message', [
                'to' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Send a template message via WhatsApp
     * Required for outbound messages when user hasn't messaged you in 24 hours
     *
     * @param string $to Recipient phone number
     * @param string $templateName Name of the approved template (e.g., "hello_world", "visitor_welcome")
     * @param array $templateParams Parameters for the template (e.g., ["John", "VIS20250115ABC123"])
     * @param string|null $language Language code (default: "en_US")
     */
    public function sendTemplateMessage(
        string $to,
        string $templateName,
        array $templateParams = [],
        ?string $language = 'en_US'
    ): bool {
        if (!$this->enabled || empty($this->apiToken) || empty($this->phoneNumberId)) {
            Log::warning('WhatsApp service is not properly configured or disabled');
            return false;
        }

        $phoneNumber = $this->formatPhoneNumber($to);
        $url = "{$this->apiUrl}/v18.0/{$this->phoneNumberId}/messages";

        $components = [];
        if (!empty($templateParams)) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(function ($param) {
                    return [
                        'type' => 'text',
                        'text' => $param,
                    ];
                }, $templateParams),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phoneNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language,
                ],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->post($url, $payload);

            $responseData = $response->json();

            if ($response->successful()) {
                if (isset($responseData['error'])) {
                    Log::error('WhatsApp template API returned error', [
                        'to' => $phoneNumber,
                        'template' => $templateName,
                        'error' => $responseData['error'],
                    ]);
                    return false;
                }

                Log::info('WhatsApp template message sent successfully', [
                    'to' => $phoneNumber,
                    'template' => $templateName,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'response' => $responseData,
                ]);
                return true;
            }

            Log::error('Failed to send WhatsApp template message', [
                'to' => $phoneNumber,
                'template' => $templateName,
                'status' => $response->status(),
                'response' => $response->body(),
                'error' => $responseData['error'] ?? null,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp template message', [
                'to' => $phoneNumber,
                'template' => $templateName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Format phone number to WhatsApp format (international format without + or spaces)
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // If phone number doesn't start with country code, add default country code
        if (strlen($phoneNumber) < 10) {
            return $this->defaultCountryCode . $phoneNumber;
        }

        // If phone number starts with 0, remove it and add country code
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = substr($phoneNumber, 1);
            return $this->defaultCountryCode . $phoneNumber;
        }

        // If phone number is 10 digits, add country code
        if (strlen($phoneNumber) === 10) {
            return $this->defaultCountryCode . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Check message status by message ID
     * This can help verify if message was actually delivered
     */
    public function checkMessageStatus(string $messageId): ?array
    {
        if (empty($this->apiToken) || empty($messageId)) {
            return null;
        }

        try {
            $url = "{$this->apiUrl}/v18.0/{$messageId}";
            
            $response = Http::withToken($this->apiToken)
                ->get($url, [
                    'fields' => 'status,errors',
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while checking WhatsApp message status', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

