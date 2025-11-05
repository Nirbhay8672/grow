<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorStoreRequest;
use App\Http\Requests\VisitorUpdateRequest;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Twilio\Rest\Client;

class VisitorController extends Controller
{
    public function index(Request $request): Response
    {
        
        $visitors = Visitor::query()
            ->orderBy('created_at', 'desc')
            ->get();

        return response($visitors);
    }

    public function store(VisitorStoreRequest $request): Response
    {
        $sid    = "ACbb792b7811fc7584ecd0fe83bf68245c";
        $token  = "1976005b7b2f024084bf78f818b6fd99";
        $twilio = new Client($sid, $token);
    
        $message = $twilio->messages
          ->create("whatsapp:+918200186326", // to
            array(
              "from" => "whatsapp:+14155238886",
              "contentSid" => "HXb5b62575e6e4ff6129ad7c8efe1f983e",
              "body" => "Your Message"
            )
          );
    
    print($message->sid);
        $validated = $request->validated();
        
        // Generate unique barcode
        $barcode = $this->generateUniqueBarcode();
        $validated['barcode'] = $barcode;

        // Generate barcode image
        $barcodeImagePath = $this->generateBarcodeImage($barcode);
        $validated['barcode_image'] = $barcodeImagePath;

        $visitor = Visitor::create($validated);
        
        // Send barcode via WhatsApp (non-blocking - errors are logged but don't fail the request)
        try {
            $this->sendBarcodeViaWhatsApp($visitor);
        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed for visitor', [
                'visitor_id' => $visitor->id,
                'error' => $e->getMessage()
            ]);
            // Continue even if WhatsApp fails
        }

        return response($visitor, 201);
    }

    public function show(Visitor $visitor): Response
    {
        return response($visitor);
    }

    public function update(VisitorUpdateRequest $request, Visitor $visitor): Response
    {
        $validated = $request->validated();

        $visitor->update($validated);

        return response($visitor);
    }

    public function destroy(Visitor $visitor): Response
    {
        $visitor->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->get();
        
        return Inertia::render('visitors/Visitors', [
            'visitors' => $visitors,
        ]);
    }

    /**
     * Generate a unique barcode
     */
    private function generateUniqueBarcode(): string
    {
        do {
            // Generate a unique barcode (combination of timestamp and random string)
            $barcode = 'VIS' . date('Ymd') . Str::upper(Str::random(8));
        } while (Visitor::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Generate QR code image (JPG) - Google Pay style
     */
    private function generateBarcodeImage(string $barcode): string
    {
        // Create directory if it doesn't exist
        $directory = 'barcodes';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // QR Code dimensions (square like Google Pay)
        $size = 500; // Size of QR code
        $padding = 40; // Padding around QR code
        $totalSize = $size + ($padding * 2);
        
        // Create image with white background
        $image = imagecreatetruecolor($totalSize, $totalSize);
        
        // Colors - Google Pay style
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $lightGray = imagecolorallocate($image, 245, 245, 245);
        
        // Fill background with white
        imagefilledrectangle($image, 0, 0, $totalSize, $totalSize, $white);
        
        // Generate QR code pattern based on barcode string
        $qrData = $this->generateQRPattern($barcode);
        $qrSize = count($qrData);
        $cellSize = $size / $qrSize;
        
        // Draw QR code pattern
        for ($y = 0; $y < $qrSize; $y++) {
            for ($x = 0; $x < $qrSize; $x++) {
                $x1 = $padding + ($x * $cellSize);
                $y1 = $padding + ($y * $cellSize);
                $x2 = $x1 + $cellSize;
                $y2 = $y1 + $cellSize;
                
                if ($qrData[$y][$x]) {
                    // Draw square with rounded corners effect
                    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $black);
                }
            }
        }
        
        // Add barcode text at the bottom (Google Pay style)
        $textY = $totalSize - 25;
        $textX = ($totalSize - (strlen($barcode) * 7)) / 2;
        
        // Use larger font for better readability
        imagestring($image, 5, $textX, $textY, $barcode, $black);
        
        // Save image
        $filename = $barcode . '.jpg';
        $filepath = $directory . '/' . $filename;
        
        // Save to public storage
        $fullPath = storage_path('app/public/' . $filepath);
        imagejpeg($image, $fullPath, 95);
        imagedestroy($image);
        
        // Return relative path for storage
        return $filepath;
    }

    /**
     * Generate QR code pattern matrix from barcode string
     * Creates a deterministic pattern that looks like a QR code
     */
    private function generateQRPattern(string $barcode): array
    {
        $size = 25; // QR code grid size (25x25 = 625 cells)
        $pattern = [];
        
        // Initialize pattern
        for ($i = 0; $i < $size; $i++) {
            $pattern[$i] = array_fill(0, $size, false);
        }
        
        // Create position markers (corners) - Google Pay style
        $this->drawPositionMarker($pattern, 0, 0, $size);
        $this->drawPositionMarker($pattern, 0, $size - 7, $size);
        $this->drawPositionMarker($pattern, $size - 7, 0, $size);
        
        // Generate data pattern from barcode string
        $hash = md5($barcode);
        $bitIndex = 0;
        
        // Fill data area (avoiding position markers and timing patterns)
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                // Skip position markers
                if (($y < 9 && $x < 9) || 
                    ($y < 9 && $x >= $size - 8) || 
                    ($y >= $size - 8 && $x < 9)) {
                    continue;
                }
                
                // Skip timing patterns
                if ($y == 6 || $x == 6) {
                    continue;
                }
                
                // Use hash bits to determine pattern
                $bit = (hexdec($hash[$bitIndex % 32]) >> ($bitIndex % 4)) & 1;
                $pattern[$y][$x] = (bool)$bit;
                $bitIndex++;
            }
        }
        
        return $pattern;
    }

    /**
     * Draw position marker (corner squares) like in QR codes
     */
    private function drawPositionMarker(array &$pattern, int $startY, int $startX, int $size): void
    {
        // Outer square (7x7)
        for ($y = 0; $y < 7; $y++) {
            for ($x = 0; $x < 7; $x++) {
                if ($startY + $y < $size && $startX + $x < $size) {
                    $pattern[$startY + $y][$startX + $x] = true;
                }
            }
        }
        
        // Inner white square (3x3)
        for ($y = 2; $y < 5; $y++) {
            for ($x = 2; $x < 5; $x++) {
                if ($startY + $y < $size && $startX + $x < $size) {
                    $pattern[$startY + $y][$startX + $x] = false;
                }
            }
        }
        
        // Center dot
        if ($startY + 3 < $size && $startX + 3 < $size) {
            $pattern[$startY + 3][$startX + 3] = true;
        }
    }

    /**
     * Display barcode image
     */
    public function showBarcode(Visitor $visitor)
    {
        if (!$visitor->barcode_image) {
            abort(404, 'Barcode image not found');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($visitor->barcode_image)) {
            // Try to regenerate if missing
            if ($visitor->barcode) {
                $barcodeImagePath = $this->generateBarcodeImage($visitor->barcode);
                $visitor->update(['barcode_image' => $barcodeImagePath]);
            } else {
                abort(404, 'Barcode image not found');
            }
        }

        // Use Storage::response() for proper file serving
        try {
            return Storage::disk('public')->response($visitor->barcode_image, null, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="' . basename($visitor->barcode_image) . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        } catch (\Exception $e) {
            // Fallback to direct file response
            $filePath = Storage::disk('public')->path($visitor->barcode_image);
            if (file_exists($filePath)) {
                return response()->file($filePath, [
                    'Content-Type' => 'image/jpeg',
                ]);
            }
            abort(404, 'Barcode image not found: ' . $e->getMessage());
        }
    }

    /**
     * Send barcode to visitor's mobile number via WhatsApp
     */
    private function sendBarcodeViaWhatsApp(Visitor $visitor): void
    {
        try {
            $mobile = $this->formatMobileNumber($visitor->mobile);
            $barcodeUrl = url('/visitors/' . $visitor->id . '/barcode');
            $barcodeImageUrl = url('/storage/' . $visitor->barcode_image);
            
            $message = "Hello {$visitor->name},\n\nYour visitor registration is successful!\n\nYour unique barcode is: {$visitor->barcode}\n\nPlease keep this barcode for your records.\n\nThank you!";

            // Priority: Twilio WhatsApp API (if configured)
            if (config('services.twilio.whatsapp_enabled', false)) {
                $this->sendViaTwilio($mobile, $message, $barcodeImageUrl);
            }
            // Option 2: Using WhatsApp Business API
            elseif (config('services.whatsapp.enabled', false)) {
                $this->sendViaWhatsAppAPI($mobile, $message);
            }
            // Option 3: Using WhatsApp Web API (like ChatAPI)
            elseif (config('services.chatapi.enabled', false)) {
                $this->sendViaChatAPI($mobile, $message);
            }
            // Option 4: Log for manual sending or use queue
            else {
                Log::info('WhatsApp message queued', [
                    'mobile' => $mobile,
                    'message' => $message,
                    'barcode_url' => $barcodeUrl,
                    'visitor_id' => $visitor->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'visitor_id' => $visitor->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception - visitor is already created
        }
    }

    /**
     * Format mobile number for WhatsApp (remove leading zeros, add country code)
     */
    private function formatMobileNumber(string $mobile): string
    {
        // Remove any non-numeric characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // Get country code from Twilio config or default
        $defaultCountryCode = config('services.twilio.default_country_code', config('services.whatsapp.default_country_code', '91'));
        
        // If mobile doesn't start with country code, add it
        if (!str_starts_with($mobile, $defaultCountryCode)) {
            // Remove leading zero if present
            $mobile = ltrim($mobile, '0');
            $mobile = $defaultCountryCode . $mobile;
        }
        
        return $mobile;
    }

    /**
     * Send via WhatsApp Business API
     */
    private function sendViaWhatsAppAPI(string $mobile, string $message): void
    {
        if (!class_exists(\GuzzleHttp\Client::class)) {
            throw new \Exception('GuzzleHttp is required for WhatsApp API. Install it via: composer require guzzlehttp/guzzle');
        }

        $apiUrl = config('services.whatsapp.api_url');
        $apiToken = config('services.whatsapp.api_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        $client = new \GuzzleHttp\Client();
        $client->post("{$apiUrl}/v1/{$phoneNumberId}/messages", [
            'headers' => [
                'Authorization' => "Bearer {$apiToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'messaging_product' => 'whatsapp',
                'to' => $mobile,
                'type' => 'text',
                'text' => ['body' => $message],
            ],
        ]);
    }

    /**
     * Send via Twilio WhatsApp
     */
    private function sendViaTwilio(string $mobile, string $message, ?string $imageUrl = null): void
    {
        if (!class_exists(\Twilio\Rest\Client::class)) {
            throw new \Exception('Twilio SDK is required. Install it via: composer require twilio/sdk');
        }

        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $from = config('services.twilio.whatsapp_from');

        // Validate Twilio configuration
        if (empty($accountSid) || empty($authToken) || empty($from)) {
            throw new \Exception('Twilio configuration is incomplete. Please check your .env file for TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, and TWILIO_WHATSAPP_FROM');
        }

        // Ensure 'from' is in whatsapp: format
        if (!str_starts_with($from, 'whatsapp:')) {
            $from = 'whatsapp:' . $from;
        }

        $client = new \Twilio\Rest\Client($accountSid, $authToken);
        
        // Send image with message if image URL is provided
        if ($imageUrl) {
            $client->messages->create(
                "whatsapp:{$mobile}",
                [
                    'from' => $from,
                    'mediaUrl' => [$imageUrl],
                    'body' => $message,
                ]
            );
        } else {
            // Send text message only
            $client->messages->create(
                "whatsapp:{$mobile}",
                [
                    'from' => $from,
                    'body' => $message,
                ]
            );
        }
    }

    /**
     * Send via ChatAPI
     */
    private function sendViaChatAPI(string $mobile, string $message): void
    {
        if (!class_exists(\GuzzleHttp\Client::class)) {
            throw new \Exception('GuzzleHttp is required for ChatAPI. Install it via: composer require guzzlehttp/guzzle');
        }

        $apiUrl = config('services.chatapi.api_url');
        $apiToken = config('services.chatapi.api_token');

        $client = new \GuzzleHttp\Client();
        $client->post("{$apiUrl}/sendMessage", [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'phone' => $mobile,
                'body' => $message,
                'token' => $apiToken,
            ],
        ]);
    }
}

