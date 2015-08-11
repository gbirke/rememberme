# Secure "Remember Me"
This library implements the best practices for implementing a secure
"Remember Me" functionality on web sites. Login information and unique secure 
tokens are stored in a cookie. If the user visits the site, the login information 
from the cookie is compared to information stored on the server. If the tokens 
match, the user is logged in. A user can have login cookies on several 
computers/browsers.

This library is heavily inspired by Barry Jaspan's article
"[Improved Persistent Login Cookie Best Practice][1]". The library protects
against the following attack scenarios:

 - The computer of a user is stolen or compromised, enabling the attacker to log
   in with the existing "Remember Me" cookie. The user knows this has happened.
   The user can remotely invalidate all login cookies.
 - An attacker has obtained the "Remember Me" cookie and has logged in with it.
   The user does not know this. The next time he tries to log in with the cookie
   that was stolen, he gets a warning and all login cookies are invalidated.
 - An attacker has obtained the database of login tokens from the server. The 
   stored tokens are hashed so he can't use them without computational effort
   (rainbow tables or brute force).
 - An attacker tries to log in with brute force, by systematically generating
   "Remember Me" cookies. With the default security settings and 100 tries per
   second (a very high number which would probably show up in the server logs), it
   would take 8 months for a 50% chance to guess a cookie value right.

## Installation

	composer require birke/rememberme

## Usage example
See the `example` directory for an example.

The example uses the file system to store the tokens on the server side. In most
cases it's better to swap the storage with the `PDOStorage` class.

## Cookie configuration
By default the cookie is valid for one week and for all paths in the domain it was set. 
It cannot be accessed/changed via JavaScript and will be transmitted on HTTP connections.
If your application requires a different configuration (for example, if you are using 
HTTPS and want to enhance security by only allowing transmission of the cookie over
the secure connection), you can create your own PHPCookie instance:

```php
$expire = strtotime("1 week", 0);
$cookie = new PHPCookie("REMEMBERME", $expire, "/", "", true, true);
$auth = new Authenticator($storage, null, $cookie);
```

## Token security
This library uses the [`openssl_random_pseudo_bytes`][2] function by default to generate a 16-byte token 
(a 32 char hexadecimal string). That should be sufficiently secure for most applications.

If you need more security, instantiate the `Authenticator` class with a custom token generator.
The following example generates Base64-encoded tokens with 128 characters:
 
 ```php
 $tokenGenerator = new DefaultToken(94, DefaultToken::FORMAT_BASE64);
 $auth = new Authenticator($storage, $tokenGenerator);
 ```
 
On systems without `openssl_random_pseudo_bytes` or with really good other entropy sources,
have a look at the [RandomLib][3]. Rememberme has a `RandomLibToken` class that can use it.

## Cleaning up expired tokens
The best way to clean expired tokens from your storage (file system or database) is to write a small script that initializes your token storage class and calls its `cleanExpiredTokens` method.
Run this script regularly with a cron job or other worker method.

If you can't run the cleanup script regularly and have a low-traffic site, you can clean the
storage on every page call by initializing the Authenticator class like this:
 
```php
 $auth = new Authenticator($storage);
 $auth->setCleanExpiredTokensOnLogin(true);
 ```

## Updating from Version 1.x
If you did subclass `Authenticator` with a custom `createToken` method, you need to
implement your token generation in a custom class that implements `TokenInterface` 
and pass it as a constructor argument. Otherwise, there is nothing to do when updating.

The less secure pseudo-random tokens of the old version will be replaced by more secure
tokens whenever a login occurs. For better security (and less convenience of your users)
you could completely clear your token storage once after updating.
 
[1]: http://jaspan.com/improved%5Fpersistent%5Flogin%5Fcookie%5Fbest%5Fpractice
[2]: http://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
[3]: https://github.com/ircmaxell/RandomLib