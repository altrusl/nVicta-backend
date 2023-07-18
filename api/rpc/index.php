<?php

require_once "./Controller.php";
require_once "./Router.php";
require_once "../src/Test.php";
require_once "../src/Notebook.php";

$controller = new rpc\Controller();

try {
    $body = file_get_contents('php://input');
    $payloadData = json_decode($body);
} catch (Throwable $th) {
    sendJson($controller->errorResponse(-32700));
}
// $response = null;

// require __DIR__ . '/Controller.php';

try {
    // batch payload
    if (is_array($payloadData)) {
        if (count($payloadData) === 0) {
            sendJson($controller->errorResponse(-32600));
        }
        $response = [];

        foreach ($payloadData as $payload) {
            $singleResponse = $controller->processRequest($payload);
            if ($singleResponse !== null) {
                $response[] = $singleResponse;
            }
        }
        if (count($response) > 0) {
            sendJson($response);
        }
        // single request
    } elseif (is_object($payloadData)) {
        $response = $controller->processRequest($payloadData);

        sendJson($response);
    } else {
        sendJson($controller->errorResponse(-32700));
    }
} catch (Throwable $th) {
    sendJson($controller->errorResponse(-32603, null, [
        'msg'   => $th->getMessage(),
        'trace' => $th->getTrace(),
    ]));
}

function sendJson($data)
{
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
}
