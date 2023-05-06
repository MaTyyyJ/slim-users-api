<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\DomainException\DomainException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Symfony\Component\String\Exception\RuntimeException;

abstract class Action
{
    protected LoggerInterface $logger;

    protected Request $request;

    protected Response $response;

    protected array $args;

    protected array $jsonRequestBody;

    public function __construct(LoggerInterface $logger,)
    {
        $this->logger = $logger;
    }

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->jsonRequestBody = $this->getJsonBody();

        try {
            return $this->action();
        } catch (DomainException $domainException) {
            throw new HttpException($this->request, $domainException->getMessage(), $domainException->getCode());
        }
    }

    abstract protected function action(): Response;

    /**
     * @return mixed
     *
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @return mixed
     *
     * @throws \RuntimeException
     */
    protected function getJsonBody(): array
    {
        if (!is_array($this->request->getParsedBody()) && $this->request->getMethod() !== "GET") {
            throw new RuntimeException("Request body is not json!");
        }

        return $this->request->getParsedBody() ?? [];
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }
}
