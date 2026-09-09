# CHANGELOG

## 2.0.5

Maintenance release: the package now has continuous integration, and a
release workflow, for the first time.

- The test suite runs on PHP 8.2 through 8.4. It was written against the
  PHPUnit 4/5 API (`PHPUnit_Framework_TestCase`, `setExpectedException()`),
  which PHPUnit 6 removed, so it could not run on any supported PHP at all.
  It now targets PHPUnit 11: 22 tests, 34 assertions.
- ADD: `phpunit/phpunit` to `require-dev`. The package had no test runner
  declared, so `composer install` produced nothing to run the tests with.
- ADD: a Continuous Integration workflow, and a Release workflow calling the
  shared `auraphp/bin` workflow.

No library code changed. `require.php` is unchanged, so nothing changes for
consumers of this package.

## 2.0.4

Hygiene release: update license year, and remove branch alias.
