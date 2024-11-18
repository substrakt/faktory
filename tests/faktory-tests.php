<?php

namespace Faktory;

class FaktoryTests extends TestCase
{
    public function test_faktory_can_create_objects()
    {
        $this->assertTrue(
            is_object(Faktory::new())
        );
    }

    public function test_faktory_creates_Faktory_Page_object_by_default()
    {
        $this->assertEquals(
            "Faktory\Page", get_class(Faktory::new()),
            "Faktory::new should return a FaktoryPost object when no parameters are passed"
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
    public function test_faktory_created_Faktory_Page_objects_have_all_expected_properties($property)
    {
        $this->assertTrue(
            property_exists(Faktory::new(), $property),
            "Faktory::new should return a FaktoryPage object with property {$property} defined"
        );
    }

    /**
     * @dataProvider wpPostPropertiesProvider
     */
    public function test_faktory_created_Faktory_Page_object_properties_can_be_set($property)
    {
        $args = [
            "ID"                => 10,
            "post_author"       => "5", # Author ID is a string, classic WordPress.
            "post_name"         => "foo-bar",
            "post_type"         => "page",
            "post_title"        => "Foo Bar",
            "post_date"         => date("Y-m-d H:i:s"),
            "post_date_gmt"     => date("Y-m-d H:i:s"),
            "post_content"      => "Foo bar baz",
            "post_excerpt"      => "Bar baz foo",
            "post_status"       => "publish",
            "comment_status"    => "closed",
            "ping_status"       => "closed",
            "post_password"     => "",
            "post_parent"       => 10,
            "post_modified"     => date("Y-m-d H:i:s"),
            "post_modified_gmt" => date("Y-m-d H:i:s"),
            "comment_count"     => "",
            "menu_order"        => "",
            "meta_input"        => [],
        ];

        $this->assertEquals(
            $args[$property], # Expected result
            Faktory::new("page", $args)->$property # Actual result
        );
    }

    /**
     */
    public function test_faktory_created_Faktory_Page_object_properties_can_be_set_with_shorthand()
    {
        $args = [
            "author"         => "5", # Author ID is a string, classic WordPress.
            "name"           => "foo-bar",
            "type"           => "page",
            "title"          => "Foo Bar",
            "date"           => date("Y-m-d H:i:s"),
            "date_gmt"       => date("Y-m-d H:i:s"),
            "content"        => "Foo bar baz",
            "excerpt"        => "Bar baz foo",
            "status"         => "publish",
            "password"       => "",
            "parent"         => 10,
            "modified"       => date("Y-m-d H:i:s"),
            "modified_gmt"   => date("Y-m-d H:i:s"),
        ];

        $map = [
            "author"         => "post_author",
            "name"           => "post_name",
            "type"           => "post_type",
            "title"          => "post_title",
            "date"           => "post_date",
            "date_gmt"       => "post_date_gmt",
            "content"        => "post_content",
            "excerpt"        => "post_excerpt",
            "status"         => "post_status",
            "password"       => "post_password",
            "parent"         => "post_parent",
            "modified"       => "post_modified",
            "modified_gmt"   => "post_modified_gmt",
            "meta"           => "meta_input",
        ];

        foreach ($args as $property => $value) {
            $key = $map[$property];
            $this->assertEquals(
                $value,
                Faktory::new("page", $args)->$key
            );
        }

        # Assert that factories that don't have a file can be used with
        # shorthand properties
        foreach ($args as $property => $value) {
            $key = $map[$property];
            $this->assertEquals(
                $value,
                Faktory::new("foo", $args)->$key
            );
        }
    }

    public function test_loaded_factories_can_be_passed_into_class()
    {
        $class = "Faktory\Page";

        $this->assertEquals(
            $class, get_class(Faktory::new()->as($class)),
            "Faktory should return an object of the class {$class} when using the 'as' method."
        );
    }

    public function test_factories_that_have_a_class_attribute_are_returned_as_objects_of_that_class()
    {
        $class = "Faktory\Page";

        $this->assertEquals(
            $class, get_class(Faktory::new("post", ["class" => $class])),
            "Faktory should return an object of the class {$class} when using the 'class' attribute."
        );
    }

    public function test_class_attribute_is_not_defined_on_created_object()
    {
        $class = "Faktory\Page";

        $this->assertFalse(
            isset(
                Faktory::new("page", ["class" => $class])->class
            ),
            "Faktory should unset the class attribute of the factories it creates"
        );
    }

    public function test_faktory_has_dirs_property()
    {
        $this->assertTrue(
            isset(Faktory::$dirs),
            "Faktory should have a public property \$dirs"
        );
    }

    public function test_meta_input_is_correctly_merged()
    {
        Faktory::addDirs(__DIR__."/factories");

        $post = Faktory::new("input", [
            "meta_input" => [
                "masthead__colour" => "#000"
            ]
        ]);

        $this->assertTrue(
            $post->meta_input['masthead__colour'] === "#000"
            &&
            # This are set in the 'input' factory
            $post->meta_input['masthead__title'] === "This is the masthead title",
            "Faktory should have a meta_input of '#000' for 'masthead__colour' and 'This is the masthead title' for 'masthead__title'"
        );
    }

    public function test_tax_input_is_correctly_merged()
    {
        Faktory::addDirs(__DIR__);

        $post = Faktory::new("input", [
            "tax_input" => [
                "categories" => ["foo", "bar"]
            ]
        ]);

        $this->assertTrue(
            $post->tax_input["categories"] === ["foo", "bar"]
            &&
            # These are set in the 'input' factory
            $post->tax_input["tags"] === ["woz", "baz"],
            "Faktory should have a tax_input ['categories' => 'foo', 'bar'] and ['tags' => 'woz', 'baz']"
        );
    }

    public function test_saving_a_factory_calls_wp_insert_post()
    {
        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_post");
        $fnc->expects($this->once())
            ->willReturn(1);

        Faktory::new()->save();
    }

    public function test_an_exception_is_raised_when_calling_wp_insert_post_fails()
    {
        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_post");
        $fnc->expects($this->once())
            ->willReturn(new \stdClass);

        $this->expectException(\Exception::class);

        Faktory::new()->save();
    }

    /**
     * @dataProvider wpTermPropertiesProvider
     */
    public function test_term_factory_is_loaded_with_correct_properties($data)
    {
        $term = Faktory::new("term");
        $this->assertTrue(isset($term->$data));
    }

    public function test_faktory_creates_a_FaktoryTerm_object_by_default_for_terms()
    {
        $this->assertEquals(
            "Faktory\Term", get_class(Faktory::new("term")),
            "Faktory should return an object of the class \Faktory\Term by default."
        );
    }

    public function test_saving_a_term_factory_calls_wp_insert_term()
    {
        $tax = $this->getFunctionMock(__NAMESPACE__, "taxonomy_exists");
        $tax->expects($this->once())
            ->willReturn(true);

        $term = $this->getFunctionMock(__NAMESPACE__, "term_exists");
        $term->expects($this->once())
            ->willReturn(NULL);

        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_term");
        $fnc->expects($this->once())
            ->willReturn(["term_id" => 1, "term_taxonomy_id" => 2]);

        $term = Faktory::new("term")->save();

        $this->assertEquals(1, $term->ID);
        $this->assertEquals(1, $term->term_id);
        $this->assertEquals(2, $term->term_taxonomy_id);
    }

    public function test_factory_terms_dont_contain_post_prefixed_properties()
    {
        $tax = $this->getFunctionMock(__NAMESPACE__, "taxonomy_exists");
        $tax->expects($this->once())
            ->willReturn(true);

        $term = $this->getFunctionMock(__NAMESPACE__, "term_exists");
        $term->expects($this->once())
            ->willReturn(NULL);

        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_term");
        $fnc->expects($this->once())
            ->willReturn(["term_id" => 1, "term_taxonomy_id" => 2]);

        $term = Faktory::new("term", ["name" => "Foo", "parent" => 1])->save();

        $this->assertFalse(isset($term->post_name), "Property post_name present on term");
        $this->assertFalse(isset($term->post_parent), "Property post_parent present on term");
    }

    /**
     * The term only stored in the database once
     */
    public function test_terms_can_be_created_more_than_once()
    {
        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_term");
        $fnc->expects($this->exactly(2))
            ->willReturn(["term_id" => 1, "term_taxonomy_id" => 2]);

        $tax = $this->getFunctionMock(__NAMESPACE__, "taxonomy_exists");
        $tax->expects($this->exactly(2))
            ->willReturn(true);

        # First return a null as the terms doesn't exist
        # Then return true because it does
        $term = $this->getFunctionMock(__NAMESPACE__, "term_exists");
        $term->expects($this->exactly(2))
            ->will($this->returnValueMap([
                [null],
                [true],
            ]));

        $term = Faktory::new("term", ["name" => "foo"])->save();
        $term = Faktory::new("term", ["name" => "foo"])->save();

        $this->assertEquals(1, $term->ID);
        $this->assertEquals(1, $term->term_id);
        $this->assertEquals(2, $term->term_taxonomy_id);
    }

    public function test_an_excexption_is_raised_when_calling_wp_insert_term_fails()
    {
        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_term");
        $fnc->expects($this->once())
            ->willReturn(new class {
                public function get_error_message() { return ""; }
            });

        $tax = $this->getFunctionMock(__NAMESPACE__, "taxonomy_exists");
        $tax->expects($this->once())
            ->willReturn(true);

        $term = $this->getFunctionMock(__NAMESPACE__, "term_exists");
        $term->expects($this->once())
            ->willReturn(NULL);

        $this->expectException(\Exception::class);

        Faktory::new("term")->save();
    }

    /**
     * Foobar is a undefined factory, but a post factory should still be returned
     * with the post_type change to 'foobar'
     */
    public function test_undefined_factory_returns_post_factory_with_post_type_changed()
    {
        $this->assertTrue(
            Faktory::new('foobar')->post_type === 'foobar',
            "Faktory::new should return a 'foobar' object when a type is passed"
        );
    }

    public function test_newMany_returns_an_array()
    {
        $this->assertTrue(
            is_array(Faktory::newMany()),
            "Faktory::newMany should return an array"
        );
    }

    public function test_newMany_returns_an_array_of_objects()
    {
        $this->assertIsObject(
            Faktory::newMany()[0],
            "Faktory::newMany should return an array of objects"
        );
    }

    /**
     * @testWith ["post_title"]
     *           ["post_title"]
     */
    public function test_newMany_increments_suffix(string $property)
    {
        $objects = Faktory::newMany(2, 'post', [$property => "Foo Bar"]);

        $this->assertEquals(
            "Foo Bar 0", $objects[0]->$property,
            "Faktory::newMany should increment the {$property} suffix"
        );

        $this->assertEquals(
            "Foo Bar 1", $objects[1]->$property,
            "Faktory::newMany should increment the {$property} suffix"
        );
    }

    public function test_newMany_instantiates_objects_as_specified_class()
    {
        $objects = Faktory::newMany(2, 'post', ["post_title" => "Foo Bar"], class: "Faktory\Page");

        $this->assertEquals(
            "Faktory\Page", get_class($objects[0]),
            "Faktory::newMany should instantiate objects as the specified class"
        );
    }
}
