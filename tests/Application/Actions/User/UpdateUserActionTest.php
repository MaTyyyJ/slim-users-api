<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use DI\Container;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    public function testCreateUserByPutSuccessfully()
    {
        $firstName = "test1 fn33";
        $lastName = "test1 2ln123123";
        $email = "test@unit.local";
        $pesel = "70092937375";
        $contactEmails = [];

        $changedData = [
            "first_name" => $firstName,
            "last_name" => $lastName,
            "pesel" => $pesel,
            "email" => $email,
            "contact_emails" => $contactEmails
        ];

        $user = User::createFromArray($changedData);

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
            ->findById(0)
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $userRepositoryProphecy
            ->findByEmailOrPesel($user->getEmail(), $user->getPesel())
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $createdUser = clone $user;
        $createdUser->setId(0);

        $userRepositoryProphecy
            ->create($user)
            ->willReturn($createdUser)
            ->shouldBeCalledOnce();


        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $request = $this->createRequest('PUT', '/users/0', json_encode($changedData));
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(StatusCodeInterface::STATUS_CREATED, ["id" => 0]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);


        $this->assertEquals($serializedPayload, $payload);
    }

    public function testUpdateUserByPutSuccessfully()
    {
        $firstName = "test1 fn33";
        $lastName = "test1 2ln123123";
        $email = "test@unit.local";
        $pesel = "70092937375";
        $contactEmails = [];

        $firstData = [
            "first_name" => $firstName,
            "last_name" => $lastName,
            "pesel" => $pesel,
            "email" => $email,
            "contact_emails" => $contactEmails
        ];

        $user = User::createFromArray($firstData);
        $user->setId(0);

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
            ->findById(0)
            ->willReturn($user)
            ->shouldBeCalledOnce();

        $userRepositoryProphecy
            ->findByEmailOrPesel($user->getEmail(), $user->getPesel())
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $changedData = [
            "first_name" => $firstName . "MOD",
            "last_name" => $lastName . "MOD",
            "pesel" => $pesel,
            "email" => $email,
            "contact_emails" => $contactEmails
        ];
        $updatedUser = User::createFromArray($changedData);
        $updatedUser->setId(0);


        $userRepositoryProphecy
            ->update($updatedUser)
            ->willReturn(1)
            ->shouldBeCalledOnce();


        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $request = $this->createRequest('PUT', '/users/0', json_encode($changedData));
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(StatusCodeInterface::STATUS_OK, ["updated" => 1]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);


        $this->assertEquals($serializedPayload, $payload);
    }
}
