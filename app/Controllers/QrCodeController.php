<?php

namespace App\Controllers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class QrCodeController extends BaseController
{
    public function generateBase64($text = null)
    {
        if ($text == null) {
            $text = $this->request->getVar('text');
        } else {
            $text = $text;
        }
        // $text = "A001-B002-C003";

        $builder = new Builder(
            writer: new PngWriter(),
            data: $text,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 200,
            margin: 10
        );

        $result = $builder->build();

        // 🔑 Conversion en base64
        $base64 = base64_encode($result->getString());
        return $base64;
        // return $this->response->setJSON([
        //     'success' => true,
        //     'qr_base64' => 'data:' . $result->getMimeType() . ';base64,' . $base64
        // ]);
    }

    public function scan()
    {
        $data = trim($this->request->getVar('data'));

        if ($data) {
            echo "✅ QR détecté php : " . htmlspecialchars($data);
        } else {
            echo "❌ Aucun QR code détecté dans l'image.";
        }
    }
}
