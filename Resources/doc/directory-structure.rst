Directory Structure
===================
This section describes the default directory structure for both source and compiled assets. The default directory structure follows Symfony's best practices and conventions as much as possible, as long as they make sense for the use case.

You're free to change this directory structure as you see fit but **we recommend you use the default** one. If you do change it, remember to update your ``gulpfile.js`` accordingly.

Here's an example of the directory structure of the source assets and the corresponding compiled assets:

.. code ::

    # Sources                    # Compiled

    app/Resources                web/assets
    ├── images                   ├── images
    │   ├── foo.png              │   ├── foo.png
    ├── scripts                  ├── js
    │   ├── app.coffee           │   ├── app.js
    └── stylesheets              └── css
        └── app.scss                 └── app.css

Source Assets
-------------
Symfony's best practices `recommend <http://symfony.com/doc/current/best_practices/web-assets.html>`_ you store your source assets under ``web/``, which means they will be publicly available. However, in our case, this doesn't make sense because those assets are meant to be compiled: you don't want your ``.scss`` or ``.coffee`` sources to be publicly available.

Having assets under ``app/Resources/`` solves that problem and has the added advantage that they're right next to the templates, under ``app/Resources/views/``, which is the `best-practice location <http://symfony.com/doc/current/best_practices/templates.html>`_ for storing templates.

Compiled Assets
---------------
Compiled assets are publicly visible so they must be stored in a directory under ``web/``. By default, they're stored under ``web/assets``.

To use a directory other than ``web/assets`` just modify your ``gulpfile.js`` accordingly:

.. code-block :: js

    // gulpfile.js

    var config = {
      buildDir: path.join(__dirname, 'web/foo'),
      // ..
    };

You also need to make sure that your bundle configuration references the correct directory:

.. code-block :: yaml

    # app/config/config.yml
    rj_frontend:
        prefix: foo
