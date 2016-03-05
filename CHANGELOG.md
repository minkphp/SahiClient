1.2.1 / 2016-03-05
==================

Testsuite:

* Added testing on PHP 7 on Travis

1.2.0 / 2015-09-21
==================

Features:

* Return the value of the condition in the `wait` method
* Added `submitForm` to submit a form
* Added `isSelected` to check for the selected check of `option`
* Implemented `getOuterHtml`

Bug fixes:

* Allow newer versions of Buzz
* Added more details in error messages in case of Sahi failure
* Fixed the attribute getter for empty attributes
* Fixed the escaping of JS values to support multiline strings
* Improved the script evaluation to preserve the type

Testsuite:

* Added testing on PHP 5.6 on Travis
* Added testing on HHVM
* Added more tests for the client
