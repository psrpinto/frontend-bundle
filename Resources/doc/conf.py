#
# Sphinx configuration file
#
# See CONTRIBUTING.md for instructions on how to build the documentation.
#

import sphinx_rtd_theme
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

project = u'frontend-bundle'

extensions = []
nitpicky = True
master_doc = 'index'
suppress_warnings = ['image.nonlocal_uri']

html_show_copyright = False
html_theme = 'sphinx_rtd_theme'
html_theme_path = [sphinx_rtd_theme.get_html_theme_path()]
html_static_path = ['_static']

# Allow omiting ``<?php`` and still have syntax highlighting
lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)

def setup(app):
    app.add_stylesheet('custom.css')
