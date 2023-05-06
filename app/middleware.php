<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
// Add Routing Middleware
    $app->addRoutingMiddleware();

// Add Body Parsing Middleware
    $app->addBodyParsingMiddleware();

};
