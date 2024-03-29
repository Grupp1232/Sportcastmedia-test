<?php
function loadTemplate($templateFileName, $variables = []) {
    extract($variables);

    ob_start();
    include  __DIR__ . '/../public_html/editjoke.html.php';
    include  __DIR__ . '/../public_html/home.html.php';
    include  __DIR__ . '/../public_html/jokes.html.php';
    include  __DIR__ . '/../public_html/layout.html.php';

    return ob_get_clean();
}

try {
    include __DIR__ . '/../public_html/DatabaseConnection.php';
    include __DIR__ . '/../public_html/DatabaseTable.php';
    include __DIR__ . '/..public_html/JokeController.php';
    include __DIR__ . '/../public_html/AuthorController.php';

    $jokesTable = new DatabaseTable($pdo, 'joke', 'id');
    $authorsTable = new DatabaseTable($pdo, 'author', 'id');

    $action = $_GET['action'] ?? 'home';
    $controllerName = $_GET['controller'] ?? 'joke';

    if ($controllerName === 'joke') {
        $controller = new JokeController($jokesTable, $authorsTable);
    }
    else if ($controllerName === 'author') {
        $controller = new AuthorController($authorsTable);
    }

    if ($action == strtolower($action) && $controllerName == strtolower($controllerName)) {
        $page = $controller->$action();
    } else {
        http_response_code(301);
        header('location: index.php?controller=' . strtolower($controllerName) .'&action=' . strtolower($action));
    }

    $title = $page['title'];

    $variables = $page['variables'] ?? [];
    $output = loadTemplate($page['template'], $variables);
    
} catch (PDOException $e) {
    $title = 'An error has occurred';

    $output = 'Database error: ' . $e->getMessage() . ' in ' .
    $e->getFile() . ':' . $e->getLine();
}

include  __DIR__ . '/../public_html/layout.html.php';
