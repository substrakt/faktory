<?php
/**
 * This file and the functions inside it exist to prevent the deprecation
 * notices in PHP8 that occur when null is passed into md5().
 * This happend when trying to mock function with PHPMock that do not exist.
 */
if (!function_exists('wp_insert_post')) {
    function wp_insert_post() {
        return;
    }
}
if (!function_exists('wp_insert_term')) {
    function wp_insert_term() {
        return;
    }
}
if (!function_exists('term_exists')) {
    function term_exists() {
        return;
    }
}
if (!function_exists('taxonomy_exists')) {
    function taxonomy_exists() {
        return;
    }
}
