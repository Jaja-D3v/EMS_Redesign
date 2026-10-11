<?php

require_once __DIR__ . '/../model/ScanQRCodeModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $action = $_GET['action'] ?? null;


    if ($action === 'getFeInfo') {
        header('Content-Type: application/json');
        $code = trim($_GET['code'] ?? '');
        if ($code === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid QR code.'
            ]);
            exit;
        }
        $data = getFeInfoByCodeController($code);
        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Fire extinguisher not found.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

        exit;
    }
}

// Get fire extinguisher by code
function getFeInfoByCodeController($code)
{
    return getFeInfoByCode($code);
}
