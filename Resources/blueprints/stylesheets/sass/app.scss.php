//
// This file is compiled to `/<?php echo $destDir; ?>/css/app.css`.
//
// Referencing other assets
// ========================
//
// To reference other assets like images or fonts, refer to them using the full
// path. For example, to reference `images/foo/bar.jpg`:
//
//     background-image: url(/images/foo/bar.jpg);
//
//     // The leading slash is optional, the following also works:
//     // background-image: url(images/foo/bar.jpg);
//
// The compiled CSS will be:
//
//     background-image: url(/<?php echo $prefix; ?>/images/foo/bar.jpg);
//
// There's no need to use relative paths, they make the code harder to reason about
// and are unnecessary:
//
//     // Don't do this!
//     background-image: url(../../images/foo/bar.jpg);
//
// Partials
// ========
//
// Partial Sass files contain snippets of reusable CSS that you can include in
// other Sass files. A partial is simply a Sass file named with a leading
// underscore, for example `_foo.scss`.
//
// Since partials are meant to be imported by other Sass files, they are not
// compiled on their own so they won't appear under `/<?php echo $destDir; ?>`.
//
// To import the file `./partials/_foo.scss`, you would do:
//
//   @import "partials/foo";
//
