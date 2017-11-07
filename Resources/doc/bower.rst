Using Bower
===========

.. note ::

    In this section it's assumed your compiled assets are under ``web/assets/``.

`Bower <http://bower.io>`_ allows you to require frontend assets in a similar way to what Composer does for PHP packages. Instead of commiting third-party code to your repository, you simply add your dependencies to a ``bower.json`` file.

For example, if you wanted to use `Bootstrap <http://getbootstrap.com>`_ you would do:

.. code-block :: shell

    bower install --save bootstrap

.. note ::

    The ``--save`` flag adds the dependency to ``bower.json``

Since Bootstrap requires jquery, Bower installed both under ``bower_components/``:

.. code ::

    bower_components/
    ├── bootstrap/
    └── jquery/

Requiring JavaScript and CSS assets
-----------------------------------
JavaScript and CSS assets installed with Bower are automatically compiled into ``web/assets/js/vendor.js`` and ``web/assets/css/vendor.css``, respectively, **along with their dependencies and in the correct order**.

.. tip ::

    For information on how this works see :ref:`bower-main-files`.

To reference the ``vendor.js`` file from your template:

.. code-block :: html

    <script src="{{ asset('js/vendor.js') }}"></script>

And to reference the ``vendor.css`` file:

.. code-block :: html

    <link href="{{ asset('css/vendor.css') }}" rel="stylesheet">

Requiring SASS or Less stylesheets
----------------------------------
SASS or Less stylesheets installed with bower, as opposed to CSS assets, are not automatically compiled into ``web/assets/css/vendor.css``. This is because they are meant to be used differently.

Depending on your use case, when requiring SASS or Less stylesheets you typically want one of the following:

1. Compile the vendor stylesheet into ``vendor.css``, while overriding variables
2. Include the vendor stylesheet in your own stylesheets so that you can use mixins, ``@extend`` rules, etc

Compiling into ``vendor.css``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
The ``app/Resources/stylesheets/vendor.scss`` (or ``.less``) file will be compiled and appended to ``web/assets/css/vendor.css`` (after any potential CSS that is automatically included from your Bower dependencies). This is useful when you simply want to compile some vendor stylesheet but don't need to use its mixins in your own stylesheets.

As an example, consider you wanted to use `Font Awesome <https://fortawesome.github.io/Font-Awesome>`_:

.. code-block :: shell

    bower install --save font-awesome

Then you need to specify that you're interested in its font assets:

.. code ::

    // bower.json

    {
      "dependencies": {
        "font-awesome": "~4.4.0"
      },
      "overrides": {
        "font-awesome": {
            "main": "fonts/*"
        }
      }
    }

.. tip ::

    For more information on why this is needed see :ref:`bower-overriding-a-packages-settings`.

When using Font Awesome, you have to set the path to the fonts directory so that ``url()`` calls use the correct path. Since font assets required with Bower are automatically "compiled" into ``web/assets/fonts/``, you would simply do:

.. code-block :: SCSS

    // app/Resources/stylesheets/vendor.scss

    // Use the absolute path to the fonts directory, relative to `web/assets`.
    // This ensures paths will be rewritten correctly when in production.
    $fa-font-path: "fonts";

    @import "../../../bower_components/font-awesome/scss/font-awesome";

If you now build your assets and look into ``web/assets/css/vendor.css`` you'll see Font Awesome's code, where the ``url()`` calls are something like:

.. code-block :: css

    url(/assets/fonts/fontawesome-webfont.eot)

Or, in production:

.. code-block :: css

    url(/assets/fonts/fontawesome-webfont.123abc.eot)

Including in your own stylesheets
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Instead of compiling a vendor stylesheet into ``vendor.css``, it's sometimes better to ``@import`` it in your own stylesheet instead. This is the case when you want to use mixins or variables defined by the vendor stylesheet.

Following Font Awesome's example above, suppose you wanted to use its mixins in your stylesheet:

.. code-block :: SCSS

    // app/Resources/stylesheets/app.scss

    $fa-font-path: "fonts";
    @import "../../../bower_components/font-awesome/scss/variables";
    @import "../../../bower_components/font-awesome/scss/mixins";

    .foo {
      @include fa-icon();
    }

.. note ::

    Note that this is just an example and not the correct usage of Font Awesome. In a real application you would never use the ``fa-icon`` mixin directly.

.. _bower-overriding-a-packages-settings:

Overriding a package's settings
-------------------------------

.. _bower-main-files:

Main Files
~~~~~~~~~~
We're able to automatically generate the ``vendor.css`` and ``vendor.js`` files from ``bower.json`` because Bower packages, in their own ``bower.json``, define their *main files*.

For example, if you look into Bootstrap's ``bower.json``, you will see something like:

.. code ::

    // bower_components/bootstrap/bower.json

    "main": [
      "less/bootstrap.less",
      "dist/js/bootstrap.js"
    ],

By parsing this file, we're able to automatically add the ``bootstrap.js`` file to our ``vendor.js``.

However, as you can see above, Bootstrap does not define ``bootstrap.css`` as a main file. If you wanted to automatically include ``bootstrap.css`` into your ``vendor.css``, you would *override* the main files defined in Bootstrap's ``bower.json`` by adding the following to your own ``bower.json``:

.. code ::

    // bower.json

    {
      "dependencies": {
        "bootstrap": "~3.3.5"
      },
      "overrides": {
        "bootstrap": {
            "main": "dist/css/bootstrap.css"
        }
      }
    }

If you then build your assets you'll see that ``bootstrap.css`` is present in your ``vendor.css``.

Dependencies in the same package
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
If you need to specify dependencies between Bower assets in a given package, you can do so in the ``overrides`` section of you ``bower.json``.

For example, instead of including all of Bootstrap's JavaScript, suppose you only needed ``popover.js``. Since ``popover.js`` requires ``tooltip.js``, your ``bower.json`` would be:

.. code ::

    // bower.json

    {
      "dependencies": {
        "bootstrap": "~3.3.5"
      },
      "overrides": {
        "bootstrap": {
            "main": [
                "js/tooltip.js",
                "js/popover.js"
            ]
        }
      }
    }

You'll then find both files in your ``vendor.js``, in the order you specified.

Dependencies between packages
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Sometimes you run into Bower packages that do not correctly specify dependencies in their ``bower.json``.

As an example, suppose you wanted to use a ``foo`` package that requires ``jquery`` but does not specify that in their ``bower.json``. You would install both packages using Bower:

.. code-block :: shell

    bower install --save foo jquery

And then setup the dependency in your ``bower.json``:

.. code ::

    // bower.json

    {
      "dependencies": {
        "foo": "0.0.1",
        "jquery": "~2.1.4"
      },
      "overrides": {
        "foo": {
            "dependencies": {
                "jquery": "*"
            }
        }
      }
    }

This ensures that, in your ``vendor.js``, ``jquery.js`` will appear before ``foo.js``.