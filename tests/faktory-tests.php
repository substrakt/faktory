<?php

class FaktoryTests extends FaktoryTestCase
{
    public function test_faktory_can_create_objects()
    {
        $this->assertTrue(
            is_object(Faktory::new())
        );
    }

    /**
     * @dataProvider wpPostTypesProvider
     */
    public function test_faktory_load_factories_of_specified_type($type)
    {
        $this->assertTrue(
            Faktory::new($type)->post_type === $type,
            "Faktory::new should return a {$type} object when a type is passed"
        );
    }

    /**
     * @dataProvider wpPostPropertiesProvider
     */
    public function test_faktory_created_FaktoryPost_objects_have_all_expected_properties($property)
    {
        $this->assertTrue(
            property_exists(Faktory::new(), $property),
            "Faktory::new should return a FaktoryPost object with property {$property} defined"
        );
    }

    /**
     * @dataProvider wpPostPropertiesProvider
     */
    public function test_faktory_created_FaktoryPost_object_properties_can_be_set($property)
    {
        $args = [
            "ID"                => 10,
            "post_author"       => '5', # Author ID is a string, classic WordPress.
            "post_name"         => 'foo-bar',
            "post_type"         => 'page',
            "post_title"        => 'Foo Bar',
            "post_date"         => date("Y-m-d H:i:s"),
            "post_date_gmt"     => date("Y-m-d H:i:s"),
            "post_content"      => 'Foo bar baz',
            "post_excerpt"      => 'Bar baz foo',
            "post_status"       => 'publish',
            "comment_status"    => 'closed',
            "ping_status"       => 'closed',
            "post_password"     => '',
            "post_parent"       => 10,
            "post_modified"     => date("Y-m-d H:i:s"),
            "post_modified_gmt" => date("Y-m-d H:i:s"),
            "comment_count"     => '',
            "menu_order"        => '',
        ];

        $this->assertEquals(
            $args[$property], # Expected result
            Faktory::new('page', $args)->$property # Actual result
        );
    }

    public function test_faktory_creates_FaktoryPost_object_by_default()
    {
        $this->assertTrue(
            get_class(Faktory::new()) === 'FaktoryPost',
            'Faktory::new should return a FaktoryPost object when no parameters are passed'
        );
    }

    public function test_loaded_factories_can_be_passed_into_class()
    {
        $class = "FaktoryPage";

        $this->assertTrue(
            get_class(Faktory::new()->as($class)) === $class,
            "Faktory should return an object of the class {$class} when using the 'as' method."
        );
    }

    public function test_factories_that_have_a_class_attribute_are_returned_as_objects_of_that_class()
    {
        $class = "FaktoryPage";

        $this->assertTrue(
            get_class(Faktory::new("post", ["class" => $class])) === $class,
            "Faktory should return an object of the class {$class} when using the 'class' attribute."
        );
    }

    public function test_class_attribute_is_not_defined_on_created_object()
    {
        $class = "FaktoryPage";

        $this->assertFalse(
            isset(
                Faktory::new("post", ["class" => $class])->class
            ),
            "Faktory should unset the class attribute of the factories it creates"
        );
    }

    public function test_faktory_has_paths_property()
    {
        $this->assertTrue(
            isset(Faktory::$paths),
            "Faktory should have a public property \$paths"
        );
    }
}
