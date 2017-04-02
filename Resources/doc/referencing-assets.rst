Referencing Assets
==================

.. note ::

    In this section it's assumed your compiled assets are located under ``web/assets/``.

In templates
------------
To reference an asset from a template, you do as you normally would, with Symfony's ``asset`` helper:

.. code-block :: html

    <img src="{{ asset('images/foo.png') }}" />

.. note ::

    You're referencing the **compiled** asset, from the ``web/assets`` directory, not the source asset.

This will automatically prefix, and when in production *cache-bust*, the URL so the previous call would ouput:

.. code-block :: html

    <img src="/assets/images/foo.png" />

Or, in production:

.. code-block :: html

    <img src="/assets/images/foo-123abc.png" />

In styleshets
-------------
It's common that you need to reference images from your stylesheets. To do that, use the ``url()`` notation and the full path to the image, relative to ``web/assets/``:

.. code-block :: css

    background-image: url(images/foo.png);

.. note ::

    Remember that you're referencing the **compiled** asset, from the ``web/assets`` directory, not the source asset.

.. tip ::

    Never reference images in stylesheets with a relative path like ``../images/foo.png``. Relative paths make the code harder to reason about, are unnecessary and will be converted to the absolute path (i.e. ``../`` is stripped).

The compiled CSS would be:

.. code-block :: css

    background-image: url(/assets/images/foo.png);

Or, in production:

.. code-block :: css

    background-image: url(/assets/images/foo-123abc.png);
