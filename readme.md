# Faktory

## Create a new PostFaktory
```php
Faktory::new();
# or
Faktory::new("post");
```

## Create a new PageFaktory
```php
Faktory::new("page");
```

## Overwrite properties
```php
Faktory::new("page", ["post_title" => "Foo"]);
```

## Save Faktory to the database
```php
$f = Faktory::new("page");
$f->save();
```
As a shortcut to this there is the `create` method.
```php
$f = Faktory::create("page");
```

## Pass the returned Faktory to a class
`Faktory::new` or `Faktory::create` will return an object. This object mimics a WordPress post object or term object. It is common practise to create another object passing in a WP_Post object as a param.
```php
$f = Faktory::new("page")->as("TheClassIWant");
# is equal to
$f = new TheClassIWant(Faktory::new("page"));
```

## Add more factories
Add a folder in your desired directory called `factories` then add the factories as PHP files. The factories must return an array. Next register the parent directory with
```php
Faktory::addDirs([
    "the/full/path/to/the/parent/dir"
]);

```
### The class attribute
The class attribute defined on a class determines which Faktory object will be returned. Currently this is either a `Faktory\Post`, `Faktory\Page` or a `Faktory\Term`. If no class attribute is present a `Faktory\Post` is returned.
```
"class" => "\Faktory\Post"
```

## Default factories
### Post
```php
# faktory/factories/post.php
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
    "post_title"        => $faker->name,
    "post_type"         => "post",
    "tax_input"         => [],
    "class"             => "\Faktory\Post",
];
```

### Page
```php
# faktory/factories/page.php
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
    "post_title"        => $faker->name,
    "post_type"         => "page",
    "tax_input"         => [],
    "class"             => "\Faktory\Page",
];
```

### Term
```php
# faktory/factories/term.php
return [
    "count"            => 0,
    "description"      => "",
    "name"             => $faker->name,
    "parent"           => 0,
    "post_author"      => "",
    "slug"             => "",
    "taxonomy"         => "",
    "term_group"       => 0,
    "term_id"          => 0,
    "term_taxonomy_id" => 0,
    "class"            => "\Faktory\Term",
];
```

## Undefined factories
Creating an undefined factories will create a factory of the type `page` but the post_type will be set to what was asked for.
```
$fixture = Faktory::new('foobar');
$fixture->post_type #=> 'foobar'
```

## Advanced Custom Field
Currently support for Advanced Custom Fields is provided through the `meta_input` argument.
```php
$fixture = Faktory::create('page', [
    'meta_input' => [
        'masthead_image__color'  => '#000',
        '_masthead_image__color' => 'field_5be99d501d261',
    ]
]);
```
Note the field reference (field_5be99d501d261) is not nessecary for primitive value types, but if you want ACF to return a different value type to what it stores then it is required. An example being ACF stores an integer for the **post_object** field but returns an instance of `WP_Post`.

If you are having to do alot of `meta_input` work inside your tests, consider moving it to a factory file instead.
