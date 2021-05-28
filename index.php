<?php
ob_start();
require __DIR__ . "/vendor/autoload.php";


use Source\Core\Session;
use CoffeeCode\Router\Router;

$session = new Session();
$route = new Router(url(),":");


/*
 * WEB ROUTES
 */
$route->namespace("Source\App");
$route->group("/");
$route->get("/","Web:home");
$route->post("/","Web:home");
$route->post("/delete","Web:delete");

/*
 * ERROR ROUTE
 */
$route->namespace("Source\App")->group("/ops");
$route->get("/{errorcode}","Web:error");

/*
 * ROUTE
 */
$route->dispatch();
/*
 * ROUTE REDIRECT
 */
if ($route->error()){
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();