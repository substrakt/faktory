<?php
use HiFolks\RandoPhp\Randomize;

$date = date("Y-m-d H:i:s");

return [
    "count"            => 0,
    "description"      => "",
    "name"             => Randomize::chars()->generate(),
    "parent"           => 0,
    "post_author"      => "",
    "slug"             => "",
    "taxonomy"         => "category",
    "term_group"       => 0,
    "term_id"          => 0,
    "term_taxonomy_id" => 0,
    "class"            => "\Faktory\Term",
];
