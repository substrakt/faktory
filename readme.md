# Faktory

Faktory is a library for creating fixtures for WordPress. It can be used to create Posts, Pages, Custom Post Types, and Terms. It is particularly useful for generating fixtures during test-driven development.

Faktory ships with factories defined for pages, posts, terms and custom post types. You can define your own factories by simply creating a corresponding PHP file and including the default properties.

To create the fake data, Faktory uses [Rando-PHP](https://github.com/Hi-Folks/rando-php).

## Creating a factory
```php
$f = Faktory::create(string $factory = "page", array|object $args = []);
```

### Parameters

#### factory
The name of the factory to create. The casing must match the casing used for the filename of the factory. This is to avoid issues with case sensitive file systems such as Linux. The builtin factory files are all lowercase.

- page (default)
- post

#### args
An array of elements to overwrite the default values. The array for the page and post factories is passed to [`wp_insert_post`](https://developer.wordpress.org/reference/functions/wp_insert_post/) to save the post to the database.
```php
$args = [
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
];
```

**Note:** the field `post_type` will be set to `post` when creating a new factory from `Faktory::create("post")`. The ID of the inserted item will be updated to match that of the corresponding row in the database.

### Return values
A `Faktory\Page` object will be returned.

### Examples

#### Creating a page factory
```php
$f = Faktory::create("page");
```
#### Creating a page factory setting some attributes
```php
$f = Faktory::create("page", ["post_title" => "About me", "post_name" => "about"]);
```

## Creating a factory for a term
The same method `Faktory::create` can be used to create term fixtures. The deafult factory is page so make sure the first parameter is set to term.

#### args
An array of elements to overwrite the default values. The array is passed as the `$args` parameter to [`wp_insert_term`](https://developer.wordpress.org/reference/functions/wp_insert_term/) to save the term to the database.
```php
$args = [
    "alias_of"    => "",
    "description" => "",
    "name"        => Randomize::chars()->generate(),
    "parent"      => 0,
    "slug"        => "",
    "taxonomy"    => "category",
];
```
### Return values
A `Faktory\Term`  object will be returned.

### Examples

#### Creating a category term
```php
$f = Faktory::create("term", ["name" => "Programming"]);
```

#### Creating a tag term
```php
$f = Faktory::create("term", ["name" => "TDD", "taxonomy" => "post_tag"]);
```

#### Creating a term for a custom taxonomy called "genre"
```php
$f = Faktory::create("term", ["name" => "Computer Science", "taxonomy" => "genre"]);
```
#### Using the terms with another Factory
```php
$term = Faktory::create("term", ["name" => "Programming"]);
$page = Faktory::create("page", [
    "tax_input" => [
        "category" => [$term->ID]
    ]
]);
```

As per the WordPress documentation if the taxonomy is hierarchical, the term list needs to be either an array of term IDs or a comma-separated string of IDs. If the taxonomy is non-hierarchical, the term list can be an array that contains term names or slugs, or a comma-separated string of names or slugs. This is because, in hierarchical taxonomy, child terms can have the same names with different parent terms, so the only way to connect them is using ID. Default empty.

Using `tax_input` is the equivalent to calling `wp_set_post_terms()` for each custom taxonomy in the array. If the current user doesn’t have the capability to work with a taxonomy, then you must use `wp_set_object_terms()` instead.

Setting the user the test are run against to an administrator will allow `tax_input` to be used. The following example is taken from the bootstrap file from the test suit at Substrakt.
```php
(function() {
    $userId = 1;
    if (empty(get_users())) {
        $userId = wp_create_user('admin', 'password');
    }
    $user = new \WP_User($userId);
    $user->set_role('administrator');
    \wp_set_current_user($userId);
})();
```

## Creating factories without saving them
If you want to create a factory but not have it saved to the database then you can use the `new` method in place of the `create` method. The `new` method takes the same arguments as the `create` method.
```php
$f = Faktory::new("page");
```
If you want to save the Faktory afterall then the `save` method can be called.

```php
$f = Faktory::new("page");
...
$f->save();
```

## Pass the returned Faktory to a class
`Faktory::create` or `Faktory::new` will return an object. This object mimics a WordPress post object or term object. You may find yourself frequently passing the created fixture as a paramter to another class. This is where the `as` method can come in useful.
```php
$f = Faktory::create("page")->as("TheClassIWant");
# is equal to
$f = new TheClassIWant(Faktory::create("page"));
```

## Creating your own factories
Add a directory called `factories` in your desired location. In this directory you will put all your custom factory files. If you create a file called, post.php, page.php or term.php it will overwrite the default factories used by Faktory. This is perfectly acceptable.

You will then need to register the directory with Faktory using the `addDirs` method. This should be done in your bootstrap file before you intend to use Faktory.

### Eaxample
```php
Faktory::addDirs([
    "the/full/path/to/your/factories/dir"
]);
```

Your factories need to return an array of data. The data that is returned depends on the type of data that is being inserted into the database. Currently Faktory supports post types and terms. To inform Faktory what type some object is to be created use the [class attribute](#the-class-attribute) with your returned array. For example see the [default factories](#default-factories).

### The class attribute
The class attribute defined on a factory determines which Faktory object will be returned. Currently this is either a `Faktory\Post`, `Faktory\Page` or a `Faktory\Term`. If no class attribute is present a `Faktory\Page` is returned.
```php
[
...,
"class" => "\Faktory\Page",
];
```
The `Faktory\Page` and `Faktory\Post` objects are identical other than in name. They exist as a way to help distinquish between the two post types. If you are creating term factories then it will be important to specific the term class.
```php
[
...,
"class" => "\Faktory\Term",
];
```

Each of the classes has a save method which is responsible for inserting the data into the database if `Faktory::create` was used.

## Default factories

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
    "post_title"        => Randomize::chars()->generate(),
    "post_type"         => "page",
    "tax_input"         => [],
    "class"             => "\Faktory\Page",
];
```

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
    "post_title"        => Randomize::chars()->generate(),
    "post_type"         => "post",
    "tax_input"         => [],
    "class"             => "\Faktory\Post",
];
```

### Term
```php
# faktory/factories/term.php
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
```

## Shorthand properties
For factories that are a post type (i.e posts, pages etc) shorthand can be used for the array keys. Below is a map showing the what the shorthand property will be transformed into.
```php
Faktory::new("page", ["title" => "Foo", "content" => "Foo bar baz"]);
```

### Shorthand keys
Here is a list of the all the shorthand keys and the name of the full key they map to.
```php
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
```

## Undefined factories
Creating an undefined factories will create a factory based on `page` but the post_type will be set to what was asked for.
### Example
```php
$fixture = Faktory::create('foobar');
$fixture->post_type #=> 'foobar'
```
This allow you to easily create fixtures for custom post types without the need to create a corresponding file in your factories directory.

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

## Contributing
We'd love you to help out with Faktory and no contribution is too small. We particularly welcome pull requests from anyone who is looking to make their first open source contribution.

Fork the Faktory and create a pull request with your changes or features in. Of course, you'll need to include some tests if the changes are to the code.

### Ideas for pull requests
- Factory for Users
- Factory for Attachments. WordPress stores attachments as posts but requires extra data to make them works as attachments.
- Option to return the native `WP_Post` and `WP_Term` objects instead of the Faktory ones.
- Documentation. You can never have enough examples.

We loosely follow the
[PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
and
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standards,
but we'll probably merge any code that looks close enough.

---
Development of Faktory is sponsored by [Substrakt](https://substrakt.com)
