frontend-bundle
===============
A modern frontend development workflow for Symfony apps

.. image:: https://img.shields.io/travis/regularjack/frontend-bundle/master.svg?style=flat-square
  :alt: Build Status
  :target: https://travis-ci.org/regularjack/frontend-bundle
.. image:: https://img.shields.io/scrutinizer/coverage/g/regularjack/frontend-bundle.svg?style=flat-square
  :alt: Coverage Status
  :target: https://scrutinizer-ci.com/g/regularjack/frontend-bundle/code-structure
.. image:: https://img.shields.io/scrutinizer/g/regularjack/frontend-bundle.svg?style=flat-square
  :alt: Scrutinizer Code Quality
  :target: https://scrutinizer-ci.com/g/regularjack/frontend-bundle
.. image:: https://insight.sensiolabs.com/projects/5f7d6dc7-1dcb-4acf-86b7-eb1564c59939/mini.png
  :alt: SensioLabsInsight
  :target: https://insight.sensiolabs.com/projects/5f7d6dc7-1dcb-4acf-86b7-eb1564c59939
.. image:: https://img.shields.io/packagist/v/regularjack/frontend-bundle.svg?style=flat-square
  :alt: Packagist Version
  :target: https://packagist.org/packages/regularjack/frontend-bundle
.. image:: https://img.shields.io/packagist/dt/regularjack/frontend-bundle.svg?style=flat-square
  :alt: Total Downloads
  :target: https://packagist.org/packages/regularjack/frontend-bundle

Symfony comes packaged with `Assetic <https://github.com/symfony/AsseticBundle>`_ for managing frontend assets like CSS, JavaScript or images. Assetic is great to quickly start a project but, as applications grow, its limitations start to show.

It has thus become more and more common to integrate tools native to frontend development into Symfony projects (`bower`, `gulp`, `webpack`, `livereload`, etc). However, setting up a seamless frontend development workflow is not easy and developers must repeat themselves every time they start a new project.

`There <https://github.com/romanschejbal/gassetic>`_ `are <https://github.com/Spea/SpBowerBundle>`_ `several <https://github.com/francoispluchino/composer-asset-plugin>`_ `tools <https://github.com/Kunstmaan/KunstmaanLiveReloadBundle>`_ out there that make it easier to do this but they come with their own limitations and many are wrappers for the native frontend development tools. Developers should be able to use the native tools directly and have them just work within their Symfony projects.

This bundle attempts to be the go-to solution for quickly, easily and cleanly setting up a tailored frontend development workflow in Symfony projects.

*Supports PHP 5.3+, Symfony 2.3+*

Features
--------
* **Asset pipeline**
    * Automatically generate the build file for your preferred asset pipeline
    * Supports `Gulp <https://github.com/gulpjs/gulp>`_, (`Webpack <https://webpack.github.io/>`_, `Broccoli <https://github.com/broccolijs/broccoli>`_ and others on the way)
    * Sensible defaults that work with most Symfony projects
    * You can easily adapt it for your use case
* **Use Symfony's native calls to reference assets**
    * ``<script src="{{ asset('js/foo.js') }}"></script>``
    * No need to clutter your Twig templates with *boundaries* for the asset pipeline
    * Assets are automatically *cache-busted* in production
* **Fast development**
    * Fast rebuilds make for an efficient workflow
    * Only changed files are processed
    * No more slow refreshes due to Assetic
* **Livereload**
    * Browser updates when you save a file
    * Change the CSS, the browser instantaneously updates, without a page reload
* **Bower**
    * Frontend dependencies are a ``bower install`` away
    * No more vendor code in your repository
    * Automatically generates ``vendor.js`` and ``vendor.css`` files from your ``bower.json``
* **Cache busting**
    * Automatically add a version to assets when in production
    * No more need to set a version on every deploy
    * An asset's version only changes if its content changed

Table of Contents
-----------------
.. toctree ::
    :maxdepth: 1

    setup
    directory-structure
    referencing-assets
    bower
    deployment

License
-------
MIT
