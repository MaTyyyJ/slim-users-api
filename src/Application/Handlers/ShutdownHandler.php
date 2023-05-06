<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\ResponseEmitter\ResponseEmitter;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;

class ShutdownHandler
{
    private Request $request;

    private HttpErrorHandler $errorHandler;

    private AMQPStreamConnection $amqpConnection;

    private Response $response;
    private bool $displayErrorDetails;
    private int $timeStarted;

    public function __construct(
        Request $request,
        HttpErrorHandler $errorHandler,
        Response $response,
        AMQPStreamConnection $amqpConnection,
        int $timeStarted,
        bool $displayErrorDetails,
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
        $this->amqpConnection = $amqpConnection;
        $this->timeStarted = $timeStarted;
    }

    public function __invoke(): void
    {
        $error = error_get_last();
        if ($error) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = "FATAL ERROR: {$errorMessage}. ";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;

                    case E_USER_WARNING:
                        $message = "WARNING: {$errorMessage}";
                        break;

                    case E_USER_NOTICE:
                        $message = "NOTICE: {$errorMessage}";
                        break;

                    default:
                        $message = "ERROR: {$errorMessage}";
                        $message .= " on line {$errorLine} in file {$errorFile}.";
                        break;
                }
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                false,
                false,
            );

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
        $channel = $this->amqpConnection->channel();
        $channel->queue_declare('http_logs', false, true, false, false);
        $amqpMessage = new AMQPMessage(
            json_encode([
                "url" => (string)$this->request->getUri(),
                "queryParams" => $this->request->getQueryParams(),
                "type" => $this->request->getMethod(),
                "code" => $this->response->getStatusCode(),
                "requestBody" => (string)$this->request->getBody(),
                "responseBody" => (string)$this->response->getBody(),
                "startedAt" => $this->timeStarted,
                "createdAt" => time(),
            ])
        );
        $channel->basic_publish($amqpMessage, '', 'http_logs');
        $channel->close();
    }
}
