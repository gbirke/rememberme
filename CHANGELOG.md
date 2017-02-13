# Change Log

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased
- Update PHPUnit dependency to 5.7
- Check in `composer.lock`

## 1.0.5 (2016-02-12)
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