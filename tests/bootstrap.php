<?php

/**
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$root    = realpath(dirname(dirname(__FILE__)));
$library = "$root/src";
$tests   = "$root/tests";

require "{$library}/Faktory.php";
require "{$library}/FaktoryPost.php";
require "{$library}/FaktoryPage.php";

require "{$tests}/FaktoryTestCase.php";

