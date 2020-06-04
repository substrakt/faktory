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

## Return class
```php
$f = Faktory::new("page");
$f->as("TheClassIWant");
# Or in the args
$f = Faktory::new("page", ["class" => "TheClassIWant"]);
```
It is also possible the set the "class" property in the factory file.

## Add more factories
Add a folder in your desired directory called `factories` then add the factories as PHP files. The factories must return an array. Next register the parent directory with
```php
Faktory::addDirs([
    "the/full/path/to/the/parent/dir"
]);
```

## Default factories
### Post
```php
# faktory/factories/post.php
$date = date("Y-m-d H:i:s");
return [
    "comment_count"     => "",
    "comment_status"    => "",
    "ID"                => 1,
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
];
```

### Page
```php
# faktory/factories/page.php
$date = date("Y-m-d H:i:s");
return [
    "comment_count"     => "",
    "comment_status"    => "",
    "ID"                => 1,
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
    "term_id"          => 1,
    "term_taxonomy_id" => 0,
];
```
