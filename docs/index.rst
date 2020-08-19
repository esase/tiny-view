.. _index-view-label:

View
====

This is essential part of any web application which allows you easily show data using html templates and layouts.

Installation
------------

Run the following to install this library:


.. code-block:: bash

    $ composer require esase/tiny-view


Template
---------

Templates usually used for displaying a part of a page. It would be like, `a login page` or `list of users`, etc.

----------------
Template example
----------------

.. code-block:: php

    <?php

        use Tiny\View\View;

        // we are going to show the user list
        $view = new View(['users' => [
                ['id' => 1, 'name' => 'Tester1'],
                ['id' => 2, 'name' => 'Tester2']
            ]],
            './users.phtml' // a template for the data
        );

        // render the template using passed variables
        echo $view;

And the `HTML` template `(users.phtml)`:

.. code-block:: html

    <ul>
        <?php foreach ($this->users as $user): ?>
            <li>
                <b><?= $this->id ?></b>: <?= $this->name ?>
            </li>
        <?php endforeach ?>
    </ul>

**Note:** In `HTML` files you are free to use any :code:`PHP` methods and operators.
Read more about the alternative syntax which is convenient way for using in templates: https://www.php.net/manual/en/control-structures.alternative-syntax.php

Layout
------

Layouts work as wrappers for generated content. In our case we may wrap the rendered `users list` in a layout
(which may include js/css files, meta, etc).
Usually there is a one universal layout and many templates but it optional.

--------------
Layout example
--------------

.. code-block:: php

    <?php

        use Tiny\View\View;

        $view = new View(['users' => [
                ['id' => 1, 'name' => 'Tester1'],
                ['id' => 2, 'name' => 'Tester2']
            ]],
            './users.phtml', // a template for the data
            './layout/base.phtml' // a layout for wrapping the data
        );

        // render both template and layout
        echo $view;

And the `HTML` layout `(base.phtml)`:

.. code-block:: html

    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>My test site</title>
        </head>
        <body>
            <div class="container">
                <!-- inject generated data from the templates -->
                <?= $this->content ?>
            </div>
        </body>
    </html>

View helpers
------------

Some times it not enough only inject variables in `templates/layouts` or call inbuilt :code:`PHP` functions.
We need to call our own functions in the template context.

To make it real the  :code:`View` uses the :ref:`Event manager <index-event-manager-label>`.
Generally speaking whenever you call an undefined method (which is not registered in the :code:`View` object)
The :code:`View`  triggers an event which includes the invoked method's name and its parameters,
and there is should be a listener which is responsible for that method.

In example bellow we implement a simple helper for printing a random value.


--------------
Helper example
--------------

.. code-block:: php

    <?php

        use Tiny\EventManager\Event;
        use Tiny\EventManager\EventManager;
        use Tiny\ServiceManager\ServiceManager;
        use Tiny\View\View;

        // create a new event manager
        $eventManager = new EventManager(
            // the event manager requires the service manager (a service locator)
            new ServiceManager([
                'ViewHelperRandom' => function() { // register a new view helper service
                    return function(Event $event) {
                        // return a random value
                        $event->setData(rand());
                    }
                }
            ])
        );

        // listen the any invocations of `$this->random()` in the View and call it from the service manager
        $eventManager->subscribe('view.call.helper.random', 'ViewHelperRandom');

        // init the View
        $view = new View(['users' => [
                ['id' => 1, 'name' => 'Tester1'],
                ['id' => 2, 'name' => 'Tester2']
            ]],
            './users.phtml'
        );

        // register the event manager in the View
        $view->setEventManager($eventManager);

        // render the template using passed variables
        echo $view;

The `HTML` template snippet:

.. code-block:: html

    <ul>
        <?php foreach ($this->users as $user): ?>
            <li>
                <b><?= $this->random() ?></b>: <?= $this->name ?>
            </li>
        <?php endforeach ?>
    </ul>
