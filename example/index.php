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
    die(
        "'$storagePath' does not exist or is not writable by the web server.\n".
        "To run the example, please create the directory and give it the correct permissions."
    );
}
$storage = new FileStorage($storagePath);
$rememberMe = new Authenticator($storage);

$router = new Birke\Rememberme\Example\Router();

$router->beforeEachRoute(function () use ($rememberMe) {

    $loginResult = $rememberMe->login();

    if ($loginResult->isSuccess()) {
        $_SESSION['username'] = $loginResult->getCredential();
        // There is a chance that an attacker has stolen the login token, so we store
        // the fact that the user was logged in via RememberMe (instead of login form)
        $_SESSION['remembered_by_cookie'] = true;

        return;
    }

    if ($loginResult->hasPossibleManipulation()) {
        render_template("cookie_was_stolen");
        exit();
    }

    // Log out when tokens have expired and user is still logged in with remember me
    // This state can happen in two cases:
    // a) The triples were cleared after an attack or a "global logout"
    // b) The triples have expired
    if ($loginResult->isExpired() && !empty($_SESSION['username']) && !empty($_SESSION['remembered_by_cookie'])) {
        $rememberMe->clearCookie();
        unset($_SESSION['username']);
        unset($_SESSION['remembered_by_cookie']);
        render_template('login', 'You were logged out because the "Remember Me" cookie was no longer valid.');
        exit;
    }

    if ($loginResult->isExpired() && !empty($_SESSION['username'])) {
        // Do rate limiting here. Lots of requests for non-existing triplets can be an indicator of a brute force attack
        sleep(5);
    }
});

$router->route('!^/login$!', function () use ($rememberMe) {
    if (empty($_POST)) {
        render_template("login");
    }

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
        exit();
    } else {
        render_template("login", "Invalid username or password, please try again.");
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
