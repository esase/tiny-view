# tiny-view

[![Build Status](https://travis-ci.com/esase/tiny-view.svg?branch=master)](https://travis-ci.com/github/esase/tiny-view/builds)
[![Coverage Status](https://coveralls.io/repos/github/esase/tiny-view/badge.svg?branch=master)](https://coveralls.io/github/esase/tiny-view?branch=master)

**Tiny/View** separates your business logic from its representation, it means you can store your `view` as a list of 
separated `.phtml` files and pass there you data keeping you controllers clean.

The package is very fast due to using native `php` inside templates and layouts, yes we don't use
any extra markup language, it's only relies on the php's [alternative syntax](https://www.php.net/control-structures.alternative-syntax)

There are only two main entities: `templates` and `layouts`.

`Templates` are used for displaying small peaces of information like list of users, a login form, etc. And the `layouts` which 
work as wrappers for those templates. For instance you can have several layouts with already included `css` and `js` files and 
It makes you life easier because you don't need to specify any `js` and `css` files for every template.

## Template

```php
<?php

    use Tiny\View\View;

    // we are going to show the user list
    $view = new View(['users' => [ // an array of users
            ['id' => 1, 'name' => 'Tester1'],
            ['id' => 2, 'name' => 'Tester2']
        ]],
        './users.phtml' // a template for the data,
        ./layout/base.phtml' // a layout it's optional 
    );

    // render the template using passed variables and wrap its content to a layout
    echo $view;

```

```html
<ul>
    <?php foreach ($this->users as $user): ?>
        <li>
            <b><?= $this->id ?></b>: <?= $this->name ?>
        </li>
    <?php endforeach ?>
</ul>
```

## Layout

```html
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>My test site</title>
        </head>
        <body>
            <div class="container">
                <!-- inject generated data from templates -->
                <?= $this->content ?>
            </div>
        </body>
    </html>
```

## View helpers

When it's not enough a pre built functionality or it might be not convenient way to work with templates 
you can register you own list of helpers (small functions) which will do exactly you want.


## Installation

Run the following to install this library:

```bash
$ composer require esase/tiny-view
```

## Documentation

https://tiny-docs.readthedocs.io/en/latest/tiny-view/docs/index.html
