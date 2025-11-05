<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorStoreRequest;
use App\Http\Requests\VisitorUpdateRequest;
use App\Models\Visitor;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
        $validated = $request->validated();

        // Generate unique barcode
        $barcode = $this->generateUniqueBarcode();
        $validated['barcode'] = $barcode;

        // Generate barcode image
        $barcodeImagePath = $this->generateBarcodeImage($barcode);
        $validated['barcode_image'] = $barcodeImagePath;

        $visitor = Visitor::create($validated);

        // Send WhatsApp message
        $this->sendWelcomeMessage($visitor);

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

        return response(204);
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
                    ($y >= $size - 8 && $x < 9)
                ) {
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
     * Send welcome message via WhatsApp
     * Note: Free-form messages only work within 24-hour window after user's last message
     */
    private function sendWelcomeMessage(Visitor $visitor): void
    {
        if (empty($visitor->mobile)) {
            return;
        }

        $whatsappService = new WhatsAppService();
        $message = "Hello {$visitor->name},\n\n";
        $message .= "Welcome! Your visitor barcode is: {$visitor->barcode}\n\n";
        $message .= "Please keep this barcode safe for your visit.";

        $whatsappService->sendMessage($visitor->mobile, $message);
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

        // Use response()->file() for serving the file with headers
        $filePath = Storage::disk('public')->path($visitor->barcode_image);
        if (file_exists($filePath)) {
            return response()->file($filePath, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="' . basename($visitor->barcode_image) . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
    }
}