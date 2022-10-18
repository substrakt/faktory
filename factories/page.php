<?php

use HiFolks\RandoPhp\Randomize;

$date = date("Y-m-d H:i:s");

return [
    "comment_count"     => "",
    "comment_status"    => "",
    "ID"                => 0,
    "meta_input"        => [],
    "menu_order"        => 0,
    "ping_status"       => "",
    "post_author"       => "",
    "post_content"      => "",
    "post_date_gmt"     => $date,
    "post_date"         => $date,
    "post_excerpt"      => "",
    "post_modified_gmt" => $date,
    "post_modified"     => $date,
    "post_name"         => "",
    "post_parent"       => 0,
    "post_password"     => "",
    "post_status"       => "publish",
    "post_title"        => Randomize::chars()->generate(),
    "post_type"         => "page",
    "tax_input"         => [],
    "class"             => "\Faktory\Page",
];
