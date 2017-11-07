Setup
=====
Installation
------------
Install with composer:

.. code-block:: shell

    composer require regularjack/frontend-bundle

Add to your ``AppKernel.php``:

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Rj\FrontendBundle\RjFrontendBundle(),
        );
    }

Node.js must be installed on your system. You can find installation instructions on `Node's website <https://nodejs.org/en/download/package-manager>`_.

Once Node is installed, run:

.. code-block:: shell

    npm install -g bower
    npm install -g gulp-cli

.. note ::

    Only gulp is supported at the moment, other asset pipelines are on the way.
    From now on, this document will assume you're using `gulp`.

Configuration
-------------
.. tip ::

    If you're starting a new project, no configuration is needed at this point and you can safely skip this step.

Symfony has the notion of an *asset package* which allows you to group related assets together. For example, you could have an *app* package and an *admin* package which you reference as follows:

.. code-block :: html

    <script src="{{ asset('js/foo.js', 'app') }}"></script>
    <script src="{{ asset('js/bar.js', 'admin') }}"></script>

When the second argument to the ``asset()`` Twig helper is omitted, you're in fact using the *default package*:

.. code-block :: html

    <script src="{{ asset('js/foo.js') }}"></script>

This bundle *overrides* the default package in order for you to not to have to pass the second argument. If you're integrating this bundle into an existing application, which expects the ``asset()`` helper to behave as it usually does, you must disable this *magic* and explicitely specify a *package* to use:

.. code-block :: yaml

    # app/config/config.yml

    rj_frontend:
        override_default_package: false
        packages:
            mypackage:
                prefix: assets

This will ensure that existing ``asset()`` calls will keep functioning as expected and you can then progressively migrate to this bundle by using the ``mypackage`` package:

.. code-block :: html

    <script src="{{ asset('js/foo.js', 'mypackage') }}"></script>

Setting up the asset pipeline
-----------------------------
A console command is provided that allows you to generate a ``gulpfile.js`` tailored for your project. The command will ask you a set of questions (Where are your source assets? Where should the compiled assets be placed? Which CSS pre-processor you wish to use? Etc.) and use your answers to generate the ``gulpfile.js``.

After running the command you'll have a functioning ``gulpfile.js`` and the directory tree for your source assets under ``app/Resources/`` (or wherever you decided to place them).

You can run the command with:

.. code-block :: shell

    app/console rj_frontend:setup

Or one of the following:

.. code-block :: shell

    # Output which commands would have been run instead of running them
    app/console rj_frontend:setup --dry-run

    # Use default values for all the options
    app/console rj_frontend:setup --no-interaction

    # Use Less and CoffeeScript, ask for the other options
    app/console rj_frontend:setup --csspre=less --coffee=true

    # Use Less and CoffeeScript, use defaults for other options
    app/console rj_frontend:setup --csspre=less --coffee=true --no-interaction

You can read about all available options with:

.. code-block :: shell

    app/console rj_frontend:setup --help

.. tip ::

    Feel free to take a look at the generated ``gulpfile.js``. Even though the file is somewhat long, it should be straightforward to understand so you'll be able to adapt it to your use case, if need be.

Livereload
----------

.. note ::

    Livereload is enabled by default in development and disabled in production.

With Livereload enabled, all the requests that return a response with a closing ``body`` tag will have the following injected into the HTML, right before ``</body>``:

.. code-block :: html

    <script src="//localhost:35729/livereload.js"></script>

If, for some reason, you need to change the URL, you can do so with the following configuration:

.. code-block :: yaml

    # app/config/config_dev.yml
    rj_frontend:
        livereload:
            url: //example.com:1234/livereload.js

.. note ::

    The configuration should be added to ``app/config/config_dev.yml`` since it does not apply in other environments.

If you wish to not have the livereload script injected, you can do so with the following configuration:

.. code-block :: yaml

    # app/config/config_dev.yml
    rj_frontend:
        livereload: false

Next steps
----------
You're done with setup! In development, simply run the following command, and leave it running. Assets will be recompiled when changed and livereload will be triggered:

.. code-block :: shell

    gulp

If you just want to build the assets but not watch for changes:

.. code-block :: shell

    gulp build

To build the assets for the production environment run:

.. code-block :: shell

    gulp build --env production
