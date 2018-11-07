<?php


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/helix/search', 'SearchController/index');
    $r->addRoute('GET', '/helix/analyze', 'AnalyzerController/loadTraffic');
    $r->addRoute('POST', '/helix/analyze', 'AnalyzerController/loadTraffic');
    $r->addRoute('GET', '/{id:\d+}/{num:\d+}', 'AdminController/indexAction');
//    $r->addRoute('GET', '/download', 'SearchController/downloadXlsResult');
//
    // Ajax requests
    $r->addRoute('POST', '/ajax/search', 'SearchController/searchFormHandler');
    $r->addRoute('POST', '/ajax/getfields', 'SearchController/getTableFields');
    $r->addRoute('POST', '/ajax/deleteTrafficTable', 'AnalyzerController/deleteTrafficTable');
    $r->addRoute('POST', '/ajax/analyze', 'AnalyzerController/analyze');
    $r->addRoute('POST', '/ajax/getallconnections', 'AnalyzerController/renderAllConnection');
    $r->addRoute('POST', '/ajax/getResultTraffic', 'AnalyzerController/getResultFromTrafficAnalyze');

//    $r->addRoute('POST', '/ajax/saveResult', 'AnalyzerController/downloadXlsResult');
});

session_start();
$_SESSION['username'] = 'sergey';


// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        header("HTTP/1.0 404 Not Found");
        require_once 'resourses/views/errors/404.php';
        return true;
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        list($class, $method) = explode("/", $handler, 2);
        $class = '\Core\Controllers\\' . $class;
        $class = new $class;
        call_user_func_array([$class,$method], $vars);
}