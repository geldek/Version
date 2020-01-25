# Version

Represents the version number created from 4 components: major, minor, build and revision. Major and minor numbers are required components, build and revision are optional. Version can be provided as a string or set of integers. Implementation provides methods for comparing 2 unique versions.

## Usage

```php
use geldek\Version;

$v1 = new Version("1.0");
$v2 = new Version(1, 1);
$v3 = new Version(1, 1, 0, 0);
$v4 = Version::parse("1.2");
$v5 = null;
$result = Version::tryParse("invalid", $v5);
```

## Comparision methods

__equals__ returns true if both versions are equal.

```php
$v1 = new Version(1, 0);
$v2 = new Version(2, 0);
$v3 = Version::parse('1.0.0.0');

$false = $v1->equals($v2);
$true = $v1->equals($v3);
```

__compareTo__ returns -1 if calling version is lower than version in parameter, 0 if versions are equal and 1 if calling version is greater than version in parameter.

```php
$v1 = new Version('1.1');
$v2 = new Version(1, 1, 0, 1);

$minus_one = $v1->compareTo($v2);
$plus_one = $v2->compareTo($1);
```
