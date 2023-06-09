<?php

declare(strict_types=1);

use App\Application\Actions\User\GetUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\CreateUserAction;
use App\Application\Actions\User\PatchUserAction;
use App\Application\Actions\User\UpdateUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello Slim!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('/{id:[0-9]+}', GetUserAction::class);
        $group->post('', CreateUserAction::class);
        $group->put('/{id:[0-9]+}', UpdateUserAction::class);
        $group->patch('/{id:[0-9]+}', PatchUserAction::class);
    });
};
