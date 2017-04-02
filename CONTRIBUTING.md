# CONTRIBUTING
This bundle is all about Developer Experience so your feedback is **essential** for us, the community, to make it better. Every issue and Pull Request will be treated with respect.

If you decide to use this bundle, please consider opening issues if:

* You had any hiccups while setting it up
* You notice something missing in the documentation
* You see something you think should be done differently
* You notice something missing that you consider would benefit the majority of the users of this bundle
* Whatever else

## Coding Standards
This project follows [Symfony's Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html). Before you submit your PR, make sure to run [PHP-CS-Fixer](http://cs.sensiolabs.org), which will automatically format the code:

    php-cs-fixer fix

## Tests
Before submitting your PR, make sure tests pass:

    composer install
    vendor/bin/phpunit

## Documentation
Documentation is built using [Sphinx](http://www.sphinx-doc.org).

### Setup
Make sure you have [pip installed](https://pip.pypa.io/en/stable/installing/) and then install the needed dependencies inside a [virtualenv](https://virtualenv.pypa.io) environment, in ~order not to pollute your system:

    sudo pip install virtualenv
    virtualenv venv
    source venv/bin/activate
    pip install -r requirements.txt

### Building the docs
Build the documentation with:

    source venv/bin/activate
    sphinx-build -E Resources/doc Resources/doc/_build

Alternatively, use `sphinx-autobuild` to watch changes and automatically refresh the browser:

    source venv/bin/activate
    sphinx-autobuild -E -B Resources/doc Resources/doc/_build
