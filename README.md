# frontend-bundle
A modern frontend development workflow for Symfony apps.

[![Build Status](https://img.shields.io/travis/regularjack/frontend-bundle/master.svg?style=flat-square)](https://travis-ci.org/regularjack/frontend-bundle)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/regularjack/frontend-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/regularjack/frontend-bundle/code-structure)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/regularjack/frontend-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/regularjack/frontend-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5f7d6dc7-1dcb-4acf-86b7-eb1564c59939/mini.png)](https://insight.sensiolabs.com/projects/5f7d6dc7-1dcb-4acf-86b7-eb1564c59939)
[![Packagist Version](https://img.shields.io/packagist/v/regularjack/frontend-bundle.svg?style=flat-square)](https://packagist.org/packages/regularjack/frontend-bundle)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](Resources/meta/LICENSE)
<!-- [![Total Downloads](https://img.shields.io/packagist/dt/regularjack/frontend-bundle.svg?style=flat-square)](https://packagist.org/packages/regularjack/frontend-bundle) -->

Symfony comes packaged with [Assetic](https://github.com/symfony/AsseticBundle) for managing frontend assets like CSS, JavaScript or images. Assetic is great to quickly start a project but, as applications grow, its limitations start to show.

It has thus become more and more common to integrate tools native to frontend development into Symfony projects (`bower`, `gulp`, `webpack`, `livereload`, etc). However, setting up a seamless frontend development workflow is not easy and developers must repeat themselves every time they start a new project.

[There](https://github.com/romanschejbal/gassetic) [are](https://github.com/Spea/SpBowerBundle) [several](https://github.com/francoispluchino/composer-asset-plugin) [tools](https://github.com/Kunstmaan/KunstmaanLiveReloadBundle) out there that make it easier to do this but they come with their own limitations and many are wrappers for the native frontend development tools. Developers should be able to use the native tools directly and have them just work within their Symfony projects.

This bundle attempts to be the go-to solution for quickly, easily and cleanly setting up a tailored frontend development workflow in Symfony projects.

*Supports PHP 5.3+, Symfony 2.3+*

# Features
* **Asset pipeline**
    * Automatically generate the build file for your preferred asset pipeline
    * Supports [Gulp](https://github.com/gulpjs/gulp) ([Webpack](https://webpack.github.io/), [Broccoli](https://github.com/broccolijs/broccoli) and others on the way)
    * Sensible defaults that work with most Symfony projects
    * You can easily adapt it for your use case
* **Use Symfony's native calls to reference assets**
    * `<script src="{{ asset('js/foo.js') }}"></script>`
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
    * Frontend dependencies are a `bower install` away
    * No more vendor code in your repository
    * Automatically generates `vendor.js` and `vendor.css` files from your `bower.json`
* **Cache busting**
    * Automatically add a version to assets when in production
    * No more need to set a version on every deploy
    * An asset's version only changes if its content changed

# Table of Contents
* [Setup](#setup)
* [Directory structure](#directory-structure)
* [Referencing assets](#referencing-assets)
* [Using Bower](#using-bower)
* [Deployment](#deployment)
* [Livereload](#livereload)

# Setup
## Installation
Install with composer:

```
composer require regularjack/frontend-bundle
```

Add to your `AppKernel.php`:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Rj\FrontendBundle\RjFrontendBundle(),
    );
}
```

Node.js must be installed on your system. You can find installation instructions on [node.js's website](https://nodejs.org/en/download/package-manager).

Once Node.js is installed, run:

```
npm install -g bower
npm install -g gulp
```

> Only gulp is supported at the moment, other asset pipelines are on the way.

> From now on, this document will assume you're using `gulp`.

## Configuration
> If you're starting a new project, no configuration is needed at this point and you can safely skip this step.

If you're integrating into an existing Symfony project, to make sure your code keeps working, you will want to keep using Symfony's default package:

```yml
# app/config/config.yml
rj_frontend:
    override_default_package: false
```

For information on why this is needed see [Overriding the default package](#overriding-the-default-asset-package).

## Setting up the asset pipeline
A console command is provided that allows you to generate a `gulpfile.js` tailored for your project. The command will ask you a set of questions (Where are your source assets? Where should the compiled assets be placed? Which CSS pre-processor you wish to use? Etc.) and use your answers to generate the `gulpfile.js`.

After running the command you'll have a functioning `gulpfile.js` and the directory tree for your source assets under `app/Resources/` (or wherever you decided to place them).

You can run the command with:
```
app/console rj_frontend:setup
```

Or one of the following:
```
# Output which commands would have been run instead of running them
app/console rj_frontend:setup --dry-run

# Use default values for all the options
app/console rj_frontend:setup --no-interaction

# Use Less and CoffeeScript, ask for the other options
app/console rj_frontend:setup --csspre=less --coffee=true

# Use Less and CoffeeScript, use defaults for other options
app/console rj_frontend:setup --csspre=less --coffee=true --no-interaction
```

You can read about all available options with:
```
app/console rj_frontend:setup --help
```

> Feel free to take a look at the generated `gulpfile.js`. Even though the file is somewhat long, it should be straightforward to understand so you'll be able to adapt it to your use case, if need be.

## You're done with setup!
In development, simply run the following command, and leave it running. Assets will be recompiled when changed and livereload will be triggered:
```
gulp
```

If you just want to build the assets but not watch for changes:
```
gulp build
```

To build the assets for the production environment run:
```
gulp build --env production
```

Continue reading for information about the [directory structure](#directory-structure) of your assets, how to [reference compiled assets](#referencing-assets) in your templates, how to [use bower](#using-bower), [deployment](#deployment) and more.

# Directory structure
This section describes the default directory structure for both source and compiled assets. The default directory structure follows Symfony's best practices and conventions as much as possible, as long as they make sense for the use case.

You're free to change this directory structure as you see fit but **we recommend you use the default** one. If you do change it, remember to update your `gulpfile.js` accordingly.

Here's an example of the directory structure of the source assets and the corresponding compiled assets:
```
# Sources                    # Compiled

app/Resources                web/assets
├── images                   ├── images
│   ├── foo.png              │   ├── foo.png
├── scripts                  ├── js
│   ├── app.coffee           │   ├── app.js
└── stylesheets              └── css
    └── app.scss                 └── app.css
```

### Source Assets
Symfony's best practices [recommend](http://symfony.com/doc/current/best_practices/web-assets.html) you store your source assets under `web/`, which means they will be publicly available. However, in our case, this doesn't make sense because those assets are meant to be compiled: you don't want your `.scss` or `.coffee` sources to be publicly available.

Having assets under `app/Resources/` solves that problem and has the added advantage that they're right next to the templates, under `app/Resources/views/`, which is the [best-practice location](http://symfony.com/doc/current/best_practices/templates.html) for storing templates.

### Compiled Assets
Compiled assets are publicly visible so they must be stored in a directory under `web/`. By default, they're stored under `web/assets`.

To use a directory other than `web/assets` just modify your `gulpfile.js` accordingly:
```js
// gulpfile.js

var config = {
  buildDir: path.join(__dirname, 'web/foo'),
  // ..
};
```

You also need to make sure that your bundle configuration references the correct directory:
```yml
# app/config/config.yml
rj_frontend:
    prefix: foo
```

# Referencing assets
> In this section it's assumed your compiled assets are under `web/assets/`.

## In templates
To reference an asset from a template, you do as you normally would, with Symfony's `asset` helper:
```html
<img src="{{ asset('images/foo.png') }}" />
```

> Note that you're referencing the **compiled** asset, from the `web/assets` directory, not the source asset.

This will automatically prefix, and when in production *cache-bust*, the URL so the previous call would ouput:
```html
<img src="/assets/images/foo.png" />
```

Or, in production:
```html
<img src="/assets/images/foo-123abc.png" />
```

## In styleshets
It's common that you need to reference images from your stylesheets. To do that, use the `url()` notation and the full path to the image, relative to `web/assets/`:
```css
background-image: url(images/foo.png);
```

> Remember that you're referencing the **compiled** asset, from the `web/assets` directory, not the source asset.

> Never reference images in stylesheets with a relative path like `../images/foo.png`. Relative paths make the code harder to reason about, are unnecessary and will be converted to the absolute path (i.e. `../` is stripped).

The compiled CSS would be:
```css
background-image: url(/assets/images/foo.png);
```

Or, in production:
```css
background-image: url(/assets/images/foo-123abc.png);
```

# Using Bower
> In this section it's assumed your compiled assets are under `web/assets/`.

[Bower](http://bower.io) allows you to require frontend assets in a similar way to what Composer does for PHP packages. Instead of commiting third-party code to your repository, you simply add your dependencies to a `bower.json` file.

For example, if you wanted to use [Bootstrap](http://getbootstrap.com/) you would do:
```
bower install --save bootstrap
```

> The `--save` flag adds the dependency to `bower.json`

Since Bootsrap requires jquery, Bower installed both under `bower_components/`:
```
bower_components/
├── bootstrap/
└── jquery/
```

## Requiring JavaScript and CSS assets
JavaScript and CSS assets installed with Bower are automatically compiled into `web/assets/js/vendor.js` and `web/assets/css/vendor.css`, respectively, **along with their dependencies and in the correct order**.

> For information on how this works see [Main files](#main-files).

To reference the `vendor.js` file from your template:
```html
<script src="{{ asset('js/vendor.js') }}"></script>
```

And to reference the `vendor.css` file:
```html
<link href="{{ asset('css/vendor.css') }}" rel="stylesheet">
```

## Requiring SASS or Less stylesheets
SASS or Less stylesheets installed with bower, as opposed to CSS assets, are not automatically compiled into `web/assets/vendor.css`. This is because they are meant to be used differently.

Depending on your use case, when requiring SASS or Less stylesheets you typically want one of the following:

1. Compile the vendor stylesheet into `vendor.css`, while overriding variables
1. Include the vendor stylesheet in your own stylesheets so that you can use mixins, `@extend` rules, etc

### Compiling into vendor.css
The `app/Resources/stylesheets/vendor.scss` (or `.less`) file will be compiled and appended to `web/assets/vendor.css` (after any potential CSS that is automatically included from your Bower dependencies). This is useful when you simply want to compile some vendor stylesheet but don't need to use its mixins in your own stylesheets.

As an example, consider you wanted to use [Font Awesome](https://fortawesome.github.io/Font-Awesome/):
```
bower install --save font-awesome
```

Then you need to specify that you're interested in its font assets:
```json
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
```
> For more information on why this is needed see [Overriding a package's settings](#overriding-a-packages-settings).

When using Font Awesome, you have to set the path to the fonts directory so that `url()` calls use the correct path. Since font assets required with Bower are automatically "compiled" into `web/assets/fonts/`, you would simply do:

```SASS
// app/Resources/stylesheets/vendor.scss

// Use the absolute path to the fonts directory, relative to `web/assets`.
// This ensures paths will be rewritten correctly when in production.
$fa-font-path: "fonts";

@import "../../../bower_components/font-awesome/scss/font-awesome";
```

If you now build your assets and look into `web/assets/vendor.css` you'll see Font Awesome's code, where the `url()` calls are something like:
```CSS
url(/assets/fonts/fontawesome-webfont.eot)
```

Or, in production:
```CSS
url(/assets/fonts/fontawesome-webfont.123abc.eot)
```

### Including in your own stylesheets
Instead of compiling a vendor stylesheet into `vendor.css`, it's sometimes better to `@import` it in your own stylesheet instead. This is the case when you want to use mixins or variables defined by the vendor stylesheet.

Following Font Awesome's example above, suppose you wanted to use its mixins in your stylesheet:
```
// app/Resources/stylesheets/app.scss

$fa-font-path: "fonts";
@import "../../../bower_components/font-awesome/scss/variables";
@import "../../../bower_components/font-awesome/scss/mixins";

.foo {
  @include fa-icon();
}
```

> Note that this is just an example and not the correct usage of Font Awesome. In a real application you would never use the `fa-icon` mixin directly.

## Overriding a package's settings
### Main Files
We're able to automatically generate the `vendor.css` and `vendor.js` files from `bower.json` because Bower packages, in their own `bower.json`, define their *main files*.

For example, if you look into Bootstrap's `bower.json`, you will see something like:
```json
// bower_components/bootstrap/bower.json

"main": [
  "less/bootstrap.less",
  "dist/js/bootstrap.js"
],
```

By parsing this file, we're able to automatically add the `bootstrap.js` file to our `vendor.js`.

However, as you can see above, Boostrap does not define `bootstrap.css` as a main file. If you wanted to automatically include `bootstrap.css` into your `vendor.css`, you would *override* the main files defined in Bootstrap's `bower.json` by adding the following to your own `bower.json`:
```json
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
```

If you then build your assets you'll see that `bootstrap.css` is present in your `vendor.css`.

### Dependencies in the same package
If you need to specify dependencies between Bower assets in a given package, you can do so in the `overrides` section of you `bower.json`.

For example, instead of including all of Bootstrap's JavaScript, suppose you only needed `popover.js`. Since `popover.js` requires `tooltip.js`, your `bower.json` would be:
```json
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
```

You'll then find both files in your `vendor.js`, in the order you specified.

### Dependencies between packages
Sometimes you run into Bower packages that do not correctly specify dependencies in their `bower.json`.

As an example, suppose you wanted to use a `foo` package that requires `jquery` but does not specify that in their `bower.json`. You would install both packages using Bower:
```
bower install --save foo jquery
```

And then setup the dependency in your `bower.json`:

```json
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
```

This ensures that, in you `vendor.js`, `jquery.js` will appear before `foo.js`.

# Deployment
> In this section it's assumed your compiled assets are under `web/assets/`.

To build your assets for the production environment:
```
gulp build --env production
```

In production, CSS and JavaScript assets are minified and all assets are revisioned so that browsers don't use a stale version from cache (make sure you configure assets to [never expire](#configuring-assets-to-never-expire)). As an example, if you have an `images/foo.png` file it will become something like `images/foo-123abc.png` where `123abc` is the hash of the file's content.

## Manifest
So, how does this magic work? How can you do:

```html
<script src="{{ asset('js/foo.js') }}"></script>
```

And have it output:
```html
<script src="/assets/js/foo-123abc.js"></script>
```

The answer is: by using a *manifest* file that maps the original filename to the filename with the hash appended. When you run `gulp build --env production` you will also get a `manifest.json` file that looks something like:

```json
{
  "js/foo.js": "js/foo-123abc.js"
}
```

### Using the manifest
Using the manifest file to generate the URLs is disabled by default. You need to enable it for the production environment:
```yml
# app/config/config_prod.yml

rj_frontend:
    manifest: true
```

### Changing the manifest path
By default, the manifest is expected to be found under `web/assets/manifest.json`. If you need to change this, you would add the following to your `app/config/config_prod.yml` file:
```yml
# app/config/config_prod.yml

rj_frontend:
    manifest: %kernel.root_dir%/../web/foo/manifest.json
```

## Configuring assets to never expire
Since an asset's filename will change if its content changes, you can safely tell browsers to cache all assets indefinitely. You can do that by having your webserver set the `Expires` header to a value in the far future, say one year.

If you're using [nginx](http://nginx.org/en/), you can do this by adding the following `location` block to the `server` configuration:
```nginx
location /assets/ {
    expires 1y;
}
```

If using [Apache](http://httpd.apache.org), make sure you have [mod_expires](http://httpd.apache.org/docs/mod/mod_expires.html) active and add the following to your configuration:
```apache
<ifmodule mod_expires.c>
    <Directory /path/to/web/assets>
        ExpiresActive on
        ExpiresDefault "access plus 1 year"
    </Directory>
</ifmodule>
```

## Using a CDN
When serving assets from a Content Delivery Network, you want to use an absolute URL, for example:
```html
<script src="//cdn.example.com/js/foo-123abc.js"></script>
```

You can do this with the following configuration:
```yml
# app/config/config_prod.yml

rj_frontend:
    prefix: //cdn.example.com/
    manifest: true
```

> Note that the [manifest](#manifest) must still be present locally in your server

You also want references between assets to use the absolute URL, like when referencing images from your stylesheets. In your `gulpfile.js` you can set an URL prefix to use in production as follows:
```js
// gulpfile.js

var config = {
  ...
  // Prepend references between assets with a prefix.
  // Will only be used in production builds.
  urlPrefix: '//cdn.example.com',
  ...
};
```

# Livereload
> Livereload is enabled by default in development and disabled in production.

With Livereload enabled, all the requests that return a response with a closing `body` tag will have the following injected into the HTML, right before `</body>`:
```html
<script src="//localhost:35729/livereload.js"></script>
```

If, for some reason, you need to change the URL, you can do so with the following configuration:
```yml
# app/config/config_dev.yml
rj_frontend:
    livereload:
        url: //example.com:1234/livereload.js
```

> Note that the configuration should be added to `app/config/config_dev.yml` since it does not apply in other environments.

If you wish to not have the livereload script injected, you can do so with the following configuration:
```yml
# app/config/config_dev.yml
rj_frontend:
    livereload: false
```

# Overriding the default asset package
Symfony has the notion of an *asset package* which allows you to group related assets together. For example, you could have an *app* package and an *admin* package which you reference as follows:

```html
<script src="{{ asset('js/foo.js', 'app') }}"></script>
<script src="{{ asset('js/bar.js', 'admin') }}"></script>
```

When the second argument to the `asset()` Twig helper is omitted, you're in fact using the *default package*:
```html
<script src="{{ asset('js/foo.js') }}"></script>
```

This bundle *overrides* the default package in order for you to not to have to pass the second argument. If you're integrating this bundle into an existing application, which expects the `asset()` helper to behave as it usually does, you must disable this *magic* and explicitely specify a *package* to use:
```yml
# app/config/config.yml

rj_frontend:
    override_default_package: false
    packages:
        mypackage:
            prefix: assets
```

This will ensure that existing `asset()` calls will keep functioning as expected and you can then progressively migrate to this bundle by using the `mypackage` package:
```html
<script src="{{ asset('js/foo.js', 'mypackage') }}"></script>
```

# Contributing
This bundle is all about Developer Experience so your feedback is **essential** for us, the community, to make it better. Every issue and Pull Request will be treated with respect.

If you decide to use this bundle, please consider opening issues if:

* You had any hiccups while setting it up
* You notice something missing in the documentation
* You see something you think should be done differently
* You notice something missing that you consider would benefit the majority of the users of this bundle
* Whatever else
