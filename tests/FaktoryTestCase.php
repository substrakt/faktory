<?php

abstract class FaktoryTestCase
extends PHPUnit\Framework\TestCase
{
    public function wpPostPropertiesProvider()
    {
        return [
            ["ID"],
            ["post_author"],
            ["post_name"],
            ["post_type"],
            ["post_title"],
            ["post_date"],
            ["post_date_gmt"],
            ["post_content"],
            ["post_excerpt"],
            ["post_status"],
            ["comment_status"],
            ["ping_status"],
            ["post_password"],
            ["post_parent"],
            ["post_modified"],
            ["post_modified_gmt"],
            ["comment_count"],
            ["menu_order"],
        ];
    }

    public function wpPostTypesProvider()
    {
        return [
            ["page"],
            ["post"],
        ];
    }
}
