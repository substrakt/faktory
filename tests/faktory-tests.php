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
    public function test_faktory_created_Faktory_Post_objects_have_all_expected_properties($property)
    {
        $this->assertTrue(
            property_exists(Faktory::new(), $property),
            "Faktory::new should return a FaktoryPost object with property {$property} defined"
        );
    }

    /**
     * @dataProvider wpPostPropertiesProvider
     */
    public function test_faktory_created_Faktory_Post_object_properties_can_be_set($property)
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

    public function test_faktory_creates_Faktory_Post_object_by_default()
    {
        $this->assertEquals(
            "Faktory\Post", get_class(Faktory::new()),
            "Faktory::new should return a FaktoryPost object when no parameters are passed"
        );
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
                Faktory::new("post", ["class" => $class])->class
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
        Faktory::addDirs(__DIR__);

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

        $term = Faktory::new()->save();
    }

    public function test_an_excexption_is_raised_when_calling_wp_insert_post_fails()
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
        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_term");
        $fnc->expects($this->once())
            ->willReturn(["term_id" => 1, "term_taxonomy_id" => 2]);

        $tax = $this->getFunctionMock(__NAMESPACE__, "taxonomy_exists");
        $tax->expects($this->once())
            ->willReturn(true);

        $term = Faktory::new("term")->save();

        $this->assertEquals(1, $term->ID);
        $this->assertEquals(1, $term->term_id);
        $this->assertEquals(2, $term->term_taxonomy_id);
    }

    public function test_an_excexption_is_raised_when_calling_wp_insert_term_fails()
    {
        $fnc = $this->getFunctionMock(__NAMESPACE__, "wp_insert_term");
        $fnc->expects($this->once())
            ->willReturn(new \stdClass);

        $this->expectException(\Exception::class);

        Faktory::new("term")->save();
    }

    public function test_asking_for_an_undefined_factory_returns()
    {
        $this->assertTrue(
            Faktory::new('foobar')->post_type === 'foobar',
            "Faktory::new should return a 'foobar' object when a type is passed"
        );
    }
}
