<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500;
        $error = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $error->setDescription($exception->getMessage());

            switch ($statusCode) {
                case 404:
                    $error->setType(ActionError::RESOURCE_NOT_FOUND);
                    break;
                case 405:
                    $error->setType(ActionError::NOT_ALLOWED);
                    break;
                case 401:
                    $error->setType(ActionError::UNAUTHENTICATED);
                    break;
                case 403:
                    $error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
                    break;
                case 400:
                    $error->setType(ActionError::BAD_REQUEST);
                    break;
                case 501:
                    $error->setType(ActionError::NOT_IMPLEMENTED);
                    break;
                case 409:
                    $error->setType(ActionError::CONFLICT);
                    break;
            }
        }

        if (
            !($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $error->setDescription($exception->getMessage());
        }

        $payload = new ActionPayload($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
