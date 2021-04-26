# Change Log

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## 4.0.1 (2021-04-26)
- Update version number in composer.json

## 4.0.0 (2021-04-04)
- Drop PHP support for versions < 7.4
- Upgrade PHPUnit from 8.x to 9.x
- Add Optional Redis Storage
- Improve cookie handling (add same site and secure attributes)
- Make hashing function configurable

## 3.0.0 (2020-05-05)
- Drop PHP support for versions < 7.2
- Upgrade PHPUnit and PHP Code Sniffer
- Upgrade to Symfony Coding Standard 3.x
- Make `PDOStorageTest` independent from MySQL by using an in-memory database.
- Create date in PHP instead of using the `NOW()` SQL function.
- Add example SQL statements 

## 2.0.1 (2017-03-15)
- Fix `PDOStorage` test. Test is still dependent on an existing MySQL database but at least it works again.
- Hash database values in PHP instead of SQL.

## 2.0 (2017-02-20)
### Added
- `CookieInterface`
- `TokenInterface` for different token generation methods
- [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer) coding style checks
- composer scripts:
  - `test` runs the unit tests
  - `cs` runs the coding style checks
  - `ci` runs both `cs` and `test`

### Changed
- `Authenticator::login` no longer returns bool/credentials. Instead, it returns a `LoginResult` instance that can be queried for the login state and the credentials from the remember me cookie. 
- Rewritten example to use a picoframework
- Update PHPUnit dependency to 5.7
- Check in `composer.lock`
- All classes that use the `StorageInterface` now have a `Storage` suffix. 

## Removed
- `Authenticator::generateToken` - If you've subclassed it with your own method, please create a `TokenInterface` implementation instead and pass it in as a dependency.
- `Authenticatot::cookieIsValid` - The `login` function now gives more high-level information on the login result.
- `Authenticatot::loginTokenWasInvalid` - Use `hasPossibleManipulation` method of `login` result object instead.

## 1.0.5 (2017-02-12)
- Changed method for generating tokens to `random_bytes` instead of `uniquid`, with backwards compatibility library for PHP < 7.0. This'll improve **security** for new tokens.
- Adjust tests to be ready for PHPUnit 5.7 

## 1.0.4 (2015-07-22)
- Fixed SQL in storage adapter.

## 1.0.3 (2015-01-22)
### Security
- Fixed race condition when generating new tokens.
- Check expiry date of tokens in SQL storage adapter.
- Improved security documentation.

## 1.0.2 (2014-03-29)
- Add MIT license file

## 1.0.1 (2014-03-28)
- Fixed composer.json

## 1.0.0 (2014-03-28)
- First release
