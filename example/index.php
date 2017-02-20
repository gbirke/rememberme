<?php

use Birke\Rememberme\Authenticator;
use Birke\Rememberme\Storage\FileStorage;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/util.php';
require_once __DIR__.'/router.php';

session_start();

// Initialize RememberMe Library with file storage
$storagePath = dirname(__FILE__)."/tokens";
if (!is_writable($storagePath) || !is_dir($storagePath)) {
    die(implode("\n", [
        "'$storagePath' does not exist or is not writable by the web server.",
        "To run the example, please create the directory and give it the correct permissions.",
    ]));
}
$storage = new FileStorage($storagePath);
$rememberMe = new Authenticator($storage);

$router = new Birke\Rememberme\Example\Router();

$router->beforeEachRoute(function () use ($rememberMe) {
    // If user is logged in, check if the remember me cookie is still ok
    if (!empty($_SESSION['username'])) {
        // Check, if the Rememberme cookie exists and is still valid.
        // If not, we log out the current session
        // This state can happen in two cases:
        // a) The cookie is invalid because the triples were cleared after an attack or a "global logout"
        // b) The cookie is invalid because the triples have expired
        if (!$rememberMe->cookieIsValid()) {
            $rememberMe->clearCookie();
        }

        return;
    }

    $loginResult = $rememberMe->login();
    if ($loginResult === false) {
        if ($rememberMe->loginTokenWasInvalid()) {
            render_template("cookie_was_stolen");
            exit();
        }

        return;
    }
    $_SESSION['username'] = $loginResult;
    // There is a chance that an attacker has stolen the login token, so we store
    // the fact that the user was logged in via RememberMe (instead of login form)
    $_SESSION['remembered_by_cookie'] = true;
});

$router->route('!^/login$!', function () use ($rememberMe) {
    if (!empty($_POST)) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        // In a real application you'd check the database if the username and password matches
        if ($username === "demo" && $password === "demo") {
            session_regenerate_id();
            $_SESSION['username'] = $username;
            // If the user wants to be remembered, create Rememberme cookie
            if (!empty($_POST['rememberme'])) {
                $rememberMe->createCookie($username);
            } else {
                $rememberMe->clearCookie();
            }
            header("Location: /");
        } else {
            render_template("login", "Invalid credentials");
        }
    } else {
        render_template("login");
    }
});

$router->route('!^/logout$!', function () use ($rememberMe) {
    $rememberMe->clearCookie();
    $_SESSION = [];
    session_regenerate_id();
    header("Location: /");
    exit();
});

$router->route('!^/completelogout$!', function () use ($rememberMe, $storage) {
    $storage->cleanAllTriplets($_SESSION['username']);
    $_SESSION = [];
    session_regenerate_id();
    $rememberMe->clearCookie();
    header("Location: /");
    exit();
});

$router->route('!^/$!', function () {
    // When user is logged in, show info page
    if (!empty($_SESSION['username'])) {
        render_template('user_is_logged_in');

        return;
    }

    render_template('login');
});


$router->execute($_SERVER['REQUEST_URI']);
