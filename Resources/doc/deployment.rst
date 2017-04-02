Deployment
==========

.. note ::

    In this section it's assumed your compiled assets are under ``web/assets/``.

To build your assets for the production environment:

.. code-block :: shell

    gulp build --env production

In production, CSS and JavaScript assets are minified and all assets are revisioned so that browsers don't use a stale version from cache. As an example, if you have an ``images/foo.png`` file it will become something like ``images/foo-123abc.png`` where ``123abc`` is the hash of the file's content.

Manifest
--------
So, how does this magic work? How can you do:

.. code-block :: html

    <script src="{{ asset('js/foo.js') }}"></script>

And have it output:

.. code-block :: html

    <script src="/assets/js/foo-123abc.js"></script>

The answer is: by using a *manifest* file that maps the original filename to the filename with the hash appended. When you run ``gulp build --env production`` you will also get a ``manifest.json`` file that looks something like:

.. code-block :: json

    {
      "js/foo.js": "js/foo-123abc.js"
    }

Using the manifest
~~~~~~~~~~~~~~~~~~
Using the manifest file to generate the URLs is disabled by default. You need to enable it for the production environment:

.. code-block :: yaml

    # app/config/config_prod.yml

    rj_frontend:
        manifest: true

Changing the manifest path
~~~~~~~~~~~~~~~~~~~~~~~~~~
By default, the manifest is expected to be found under ``web/assets/manifest.json``. If you need to change this, you would add the following to your ``app/config/config_prod.yml`` file:

.. code-block :: yaml

    # app/config/config_prod.yml

    rj_frontend:
        manifest: "%kernel.root_dir%/../web/foo/manifest.json"

Configuring assets to never expire
----------------------------------
Since an asset's filename will change if its content changes, you can safely tell browsers to cache all assets indefinitely. You can do that by having your webserver set the ``Expires`` header to a value in the far future, say one year.

If you're using `nginx <http://nginx.org/en>`_, you can do this by adding the following ``location`` block to the ``server`` configuration:

.. code-block :: nginx

    location /assets/ {
        expires 1y;
    }

If using `Apache <http://httpd.apache.org>`_, make sure you have `mod_expires <http://httpd.apache.org/docs/mod/mod_expires.html>`_ active and add the following to your configuration:

.. code-block :: apache

    <ifmodule mod_expires.c>
        <Directory /path/to/web/assets>
            ExpiresActive on
            ExpiresDefault "access plus 1 year"
        </Directory>
    </ifmodule>

Using a CDN
-----------
When serving assets from a Content Delivery Network, you want to use an absolute URL, for example:

.. code-block :: html

    <script src="//cdn.example.com/js/foo-123abc.js"></script>

You can do this with the following configuration:

.. code-block :: yaml

    # app/config/config_prod.yml

    rj_frontend:
        prefix: //cdn.example.com/
        manifest: true

.. note ::

    The manifest file must still be present locally in your server

You also want references between assets to use the absolute URL, like when referencing images from your stylesheets. In your ``gulpfile.js`` you can set an URL prefix to use in production as follows:

.. code-block :: js

    // gulpfile.js

    var config = {
      ...
      // Prepend references between assets with a prefix.
      // Will only be used in production builds.
      urlPrefix: '//cdn.example.com',
      ...
    };
