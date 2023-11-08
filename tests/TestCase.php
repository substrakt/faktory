<?php

namespace Faktory;

abstract class TestCase
extends \PHPUnit\Framework\TestCase
{
    use \phpmock\phpunit\PHPMock;

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

    public function wpTermPropertiesProvider()
    {
        return [
            ["count"],
            ["description"],
            ["name"],
            ["parent"],
            ["slug"],
            ["taxonomy"],
            ["term_group"],
            ["term_id"],
            ["term_taxonomy_id"],
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
