<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class CreateUserActionTest extends TestCase
{
    public function testCreateUserSuccessfully()
    {
        $firstName = "test1 fn";
        $lastName = "test1 ln";
        $email = "test@unit.local";
        $pesel = "70092937375";
        $contactEmails = [];

        $body = [
            "first_name" => $firstName,
            "last_name" => $lastName,
            "pesel" => $pesel,
            "email" => $email,
            "contact_emails" => $contactEmails
        ];

        $user = User::createFromArray($body);

        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        /** @var Container $container */
        $container = $app->getContainer();

        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findByEmailOrPesel($email, $pesel)
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $createdUser = clone $user;
        $createdUser->setId(0);

        $userRepositoryProphecy
            ->create($user)
            ->willReturn($createdUser)
            ->shouldBeCalledOnce();

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());


        $request = $this->createRequest('POST', '/users', json_encode($body));
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(201, [User::ID => 0]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
