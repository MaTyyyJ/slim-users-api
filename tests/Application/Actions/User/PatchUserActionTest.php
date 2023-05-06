<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use DI\Container;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class PatchUserActionTest extends TestCase
{
    public function testPatchUserSuccessfully()
    {
        $firstName = "test1 fn33";
        $lastName = "test1 2ln123123";
        $email = "test@unit.local";
        $pesel = "70092937375";
        $contactEmails = [];

        $user = new User();
        $user->setId(0);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPesel($pesel);
        $user->setContactEmails(json_encode($contactEmails));

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

        $changedData = [
            "first_name" => "patched fn",
            "last_name" => "patched ln"
        ];

        $userChanged = clone $user;
        $userChanged->setFirstName($changedData[User::FIRST_NAME]);
        $userChanged->setLastName($changedData[User::LAST_NAME]);

        $userRepositoryProphecy
            ->update($userChanged)
            ->willReturn(1)
            ->shouldBeCalledOnce();


        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $request = $this->createRequest('PATCH', '/users/0', json_encode($changedData));
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(StatusCodeInterface::STATUS_OK, ["updated" => 1]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);


        $this->assertEquals($serializedPayload, $payload);
    }
    public function testPatchUserWithNoDataChanged()
    {
        $firstName = "test1 fn";
        $lastName = "test1 2ln123123";
        $email = "test@unit.local";
        $pesel = "70092937375";
        $contactEmails = [];

        $user = new User();
        $user->setId(0);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPesel($pesel);
        $user->setContactEmails(json_encode($contactEmails));

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

        $changedData = [
            "first_name" => "patched fn",
            "last_name" => "patched ln"
        ];

        $userChanged = clone $user;
        $userChanged->setFirstName($changedData[User::FIRST_NAME]);
        $userChanged->setLastName($changedData[User::LAST_NAME]);

        $userRepositoryProphecy
            ->update($userChanged)
            ->shouldNotBeCalled();

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $body = [
            "first_name" => $firstName,
            "last_name" => $lastName
        ];
        $request = $this->createRequest('PATCH', '/users/0', json_encode($body));
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedPayload = new ActionPayload(StatusCodeInterface::STATUS_NOT_MODIFIED, ["updated" => 0]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
    public function testPatchUserNotExists()
    {
        $firstName = "test1 fn";
        $lastName = "test1 ln";
        $email = "test@unit.local";
        $pesel = "70092937375";
        $contactEmails = [];

        $user = new User();
        $user->setId(0);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setContactEmails(json_encode($contactEmails));

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


        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $body = [
            "first_name" => $firstName,
            "last_name" => $lastName,
            "pesel" => $pesel,
            "email" => $email,
            "contact_emails" => $contactEmails
        ];
        $request = $this->createRequest('PATCH', '/users/0', json_encode($body));
        $response = $app->handle($request);

        $payload = (string)$response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
