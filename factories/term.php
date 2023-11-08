<?php

use HiFolks\RandoPhp\Randomize;

return [
    "count"            => 0,
    "description"      => "",
    "name"             => Randomize::chars()->generate(),
    "parent"           => 0,
    "slug"             => "",
    "alias_of"         => "",
    "taxonomy"         => "category",
    "term_group"       => 0,
    "term_id"          => 0,
    "term_taxonomy_id" => 0,
    "class"            => "\Faktory\Term",
];
