<?php

namespace rpc;

use rpc\Router;

class Controller
{

    /**
     * Process single JSON-RPC request.
     *
     * @param object $paylod  Request object
     * @param mixed  $payload
     *
     * @return object Response object
     */
    public function processRequest($payload)
    {
        if (!is_object($payload)) {
            return $this->errorResponse(-32700);
        }
        if (!property_exists($payload, 'jsonrpc') && !property_exists($payload, 'method')) {
            return $this->errorResponse(-32600);
        }
        $payload->context = new \stdClass();
        // if ($this->request->currentUser ?? null) {
        //     $payload->context->user = $this->request->currentUser;
        // }

        $router = new Router();

        $route = $router->route($payload->method);
        if (!$route) {
            return $this->errorResponse(-32601, $payload->id);
        }

        [$className, $methodName] = explode('::', $route);
        // $className                = $router->basePath . $className;
        $className                = $className;
        $outcome                  = (new $className())->{$methodName}($payload->params);

        if (!property_exists($payload, 'id') || !$outcome) {
            return null;
        }

        $data = [
            'jsonrpc' => '2.0',
            'id'      => $payload->id,
        ];

        return array_merge($data, (array) $outcome);
    }

    /**
     * Used for generic failures.
     *
     * @param int        $errorCode according to JSON-RPC specification
     * @param mixed|null $id
     * @param mixed      $data
     *
     * @return object Response object for this error
     */
    public function errorResponse($errorCode, $id = null, $data = [])
    {
        $response = [
            'jsonrpc' => '2.0',
            'error'   => [
                'code'    => $errorCode,
                'message' => '',
            ],
            'id' => $id,
        ];
        if ($data) {
            $response['error']['data'] = $data;
        }
        $response['error']['data']['i18n'] = 'internal_error';

        switch ($errorCode) {
            case '-32600':
                $response['error']['message'] = 'Invalid Request';
                break;

            case '-32700':
                $response['error']['message'] = 'Parse error';
                break;

            case '-32601':
                $response['error']['message'] = 'Method not found';
                break;

            case '-32602':
                $response['error']['message'] = 'Invalid params';
                break;

            case '-32603':
                $response['error']['message'] = 'Internal error';
                break;

            default:
                $response['error']['message'] = 'Internal error';
                break;
        }

        return $response;
    }
}
