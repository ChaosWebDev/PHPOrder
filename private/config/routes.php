<?php
// ! $router->get() and $router->post() need 3 variables minimum, but may have a 4th (params) as an array:
// * (uri, namespace, controller@view)
// * uri comes from $_SERVER['REQUEST_URI']
// * namespace refers to the namespace of the class you want it to call. If you have `namespace order\controller` then the namespace portion should be "order\controller"
// * controller@view is the class being called and the method to send it to. ViewController has a public function setView(), so it's ViewController@setView
// * params are any variables you want to pass to the class@method. Be sure it's in an array format, or null
// ! This does not pass variables from the URI at this time

// * TEMPLATE (GET) ROUTES * //
$router->get('/', "ChaosWD\Controller", 'TemplateController@getView'); // * You can set the default view file by assigning the name (without the .php) to $_ENV['HOME_URI']. If it is not set, it will assume `index`.
$router->get('/login', "ChaosWD\Controller", 'TemplateController@getView');
$router->get('/' . $_ENV['HOME_URL'] ?? "index", "ChaosWD\Controller", 'TemplateController@getView');

// * GET ROUTES * //
$router->get('/logout', "ChaosWD\Controller", "UserController@logout");

// * POST ROUTES * //
$router->post('/loginVerification', "ChaosWD\Controller", "UserController@validateUser");
