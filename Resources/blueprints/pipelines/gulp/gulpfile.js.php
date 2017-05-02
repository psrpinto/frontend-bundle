var path       = require('path');
var del        = require('del');
var gulp       = require('gulp');
var util       = require('gulp-util');
var cached     = require('gulp-cached');
var concat     = require('gulp-concat');
var filter     = require('gulp-filter');
var add        = require('gulp-add-src');
var flatten    = require('gulp-flatten');
var replace    = require('gulp-replace');
var babel      = require('gulp-babel');
var uglify     = require('gulp-uglify');
var minify     = require('gulp-minify-css');
var livereload = require('gulp-livereload');
var revision   = require('gulp-rev-all');
var env        = require('gulp-environments');
<?php if ($cssPre === 'sass') { ?>
var sass       = require('gulp-sass');
<?php } elseif ($cssPre === 'less') { ?>
var less       = require('gulp-less');
<?php } ?>
<?php if ($coffee) { ?>
var coffee     = require('gulp-coffee');
<?php } ?>

var production  = env.production;
var development = env.development;

var config = {
  // Full path to the directory containing the source assets.
  srcDir: path.join(__dirname, <?php echo "'$srcDir'"; ?>),

  // Full path to the directory where compiled assets will be placed.
  // Must be a directory under `web/`.
  buildDir: path.join(__dirname, <?php echo "'$destDir'"; ?>),

  // Prepend references between assets with a prefix.
  // Will only be used in production builds.
  // urlPrefix: '//cdn.example.com',

  // Patterns for each kind of asset.
  // Relative to `config.srcDir`.
  sources: {
    images: 'images/**/*',
    stylesheets: ['stylesheets/**/*.<?php echo $stylesheetExtension; ?>', '!stylesheets/_*', '!stylesheets/vendor.<?php echo $stylesheetExtension; ?>'],
    scripts: 'scripts/**/*.<?php if ($coffee) { ?>coffee<?php } else { ?>js<?php } ?>'
  },

  // Sub-directories where compiled assets will be placed.
  // Relative to `config.buildDir`.
  targets: {
    images: 'images',
    stylesheets: 'css',
    scripts: 'js',
    fonts: 'fonts'
  },

  // Vendor paths
  vendor: {
    stylesheet: path.join('stylesheets', 'vendor.<?php echo $stylesheetExtension; ?>'),
    bowerFile: path.join(__dirname, 'bower.json'),
    bowerComponents: path.join(__dirname, 'bower_components')
  },
};

// So that patterns passed to `gulp.src` are relative to `config.srcDir`
process.chdir(config.srcDir);

// Running `gulp` with no arguments will run the `watch` task
gulp.task('default', ['watch']);

/**
 * Copy images to their target directory.
 */
gulp.task('images', function() {
  return gulp.src(config.sources.images)
    .pipe(cached('images'))
    .pipe(gulp.dest(path.join(config.buildDir, config.targets.images)))
    .pipe(livereload());
});

/**
 * Compile stylesheets and place them in their target directory.
 */
gulp.task('stylesheets', function() {
  return gulp.src(config.sources.stylesheets)
    .pipe(cached('stylesheets'))
<?php if ($cssPre === 'sass') { ?>
    .pipe(sass())
<?php } elseif ($cssPre === 'less') { ?>
    .pipe(less())
<?php } ?>
    .pipe(rewriteCssUrls())
    .pipe(production(minify()))
    .pipe(gulp.dest(path.join(config.buildDir, config.targets.stylesheets)))
    .pipe(livereload());
});

/**
 * Compile scripts and place them in their target directory.
 */
gulp.task('scripts', function() {
  return gulp.src(config.sources.scripts)
    .pipe(cached('scripts'))
<?php if ($coffee) { ?>
    .pipe(coffee())
<?php } ?>
    .pipe(babel())
    .pipe(production(uglify()))
    .pipe(gulp.dest(path.join(config.buildDir, config.targets.scripts)))
    .pipe(livereload());
});

/**
 * All the vendor tasks.
 */
gulp.task('vendor', ['vendor:fonts', 'vendor:stylesheets', 'vendor:scripts']);

/**
 * Copy font files required with Bower into their target directory.
 */
gulp.task('vendor:fonts', function() {
  return gulp.src(config.vendor.bowerFile)
    .pipe(bower())
    .pipe(filter(['**/fonts/*', '**/font/*']))
    .pipe(flatten())
    .pipe(gulp.dest(path.join(config.buildDir, config.targets.fonts)))
    .pipe(livereload());
});

/**
 * Concatenate stylesheets required with Bower into `vendor.css`
 * AND
 * Compile `config.vendor.stylesheet` and append it to `vendor.css`.
 */
gulp.task('vendor:stylesheets', function() {
  return gulp.src(config.vendor.bowerFile)
    .pipe(bower())
    .pipe(filter('**/*.css'))
    .pipe(add(config.vendor.stylesheet))
<?php if ($cssPre === 'sass') { ?>
    .pipe(sass())
<?php } elseif ($cssPre === 'less') { ?>
    .pipe(less())
<?php } ?>
    .pipe(rewriteCssUrls())
    .pipe(concat('vendor.css'))
    .pipe(production(minify()))
    .pipe(gulp.dest(path.join(config.buildDir, config.targets.stylesheets)))
    .pipe(livereload());
});

/**
 * Concatenate scripts required with Bower into `vendor.js`.
 */
gulp.task('vendor:scripts', function() {
  return gulp.src(config.vendor.bowerFile)
    .pipe(bower())
    .pipe(filter('**/*.js'))
    .pipe(concat('vendor.js'))
    .pipe(production(uglify()))
    .pipe(gulp.dest(path.join(config.buildDir, config.targets.scripts)))
    .pipe(livereload());
});

/**
 * Build all assets.
 *
 * In production, every file will be revisioned by appending the hash of its
 * content to the filename, e.g. `foo.png` becomes `foo.abc123.png`. References
 * in other files will be rewritten in order to use the revisioned name.
 */
gulp.task('build', ['clean', 'images', 'stylesheets', 'scripts', 'vendor'], function(cb) {
  if (development()) {
    return cb();
  }

  var rev = new revision({
    fileNameManifest: 'manifest.json',
    hashLength: 24,
    prefix: getUrlPrefix()
  });

  var sourceContents = require('fs').readdirSync(config.buildDir).map(function(entry) {
    return path.join(config.buildDir, entry);
  });

  return gulp.src(config.buildDir + '/**/*')
    // move all files and directories to `src/`
    .pipe(gulp.dest(path.join(config.buildDir, 'src')))
      .on('end', function() {
        del.sync(sourceContents, {force: true});
      })
    // revision assets in `src/` and place them in `config.buildDir`
    .pipe(rev.revision())
    .pipe(gulp.dest(config.buildDir))
    .pipe(rev.manifestFile())
    .pipe(gulp.dest(config.buildDir))
    // remove `src/`
      .on('end', function() {
        del.sync(path.join(config.buildDir, 'src'), {force: true});
      });
});

/**
 * Trigger the right task when files change.
 */
gulp.task('watch', ['build'], function() {
  var watch = function(globs, taskName) {
    gulp.watch(globs, [taskName]).on('change', function(event) {
      if (event.type === 'deleted') {
        delete cached.caches[taskName][event.path];
      }
    });
  };

  livereload.listen();

  watch(config.sources.images, 'images');
  watch(config.sources.stylesheets, 'stylesheets');
  watch(config.sources.scripts, 'scripts');

  watch(config.vendor.bowerFile, 'vendor');
  watch(config.vendor.bowerComponents + '/**/*.js', 'vendor:scripts');
  watch([config.vendor.bowerComponents + '/**/*.css', config.vendor.stylesheet], 'vendor:stylesheets');
});

/**
 * Clean up the build directory
 */
gulp.task('clean', function(cb) {
  del.sync([path.join(config.buildDir, '*')], {force: true});
  cb();
});

/**
 * Rewrite `url()` calls in CSS files.
 *
 * Given 'assets' as `prefix`, the following `url()` calls:
 *
 * url(images/foo.jpg);
 * url('images/foo.jpg');
 * url("images/foo.jpg");
 * url(/images/foo.jpg);
 * url('/images/foo.jpg');
 * url("/images/foo.jpg");
 * url('../images/foo.jpg');
 * url('../../images/foo.jpg');
 *
 *  would be rewritten to:
 *  - development: url(/assets/images/foo.jpg)
 *  - production:  url(/images/foo.jpg)
 *
 * When in production, the prefix is not prepended because the `revision` step of
 * the build will do that.
 */
var rewriteCssUrls = function() {
  return replace(
    /url\(['"]?(?!data:)(\.\.\/)*\/?([^'"]+?)['"]?\)/g,
    development() ? 'url(' + getUrlPrefix() + '/$2)' : 'url(/$2)'
  );
};

/**
 * Retrieve the prefix to prepend to URLs.
 *
 * In development, the prefix is the directory under `web/`, e.g. '/assets' if
 * `config.buildDir` is `/path/to/web/assets`.
 *
 * In production, if `config.urlPrefix` is defined, it will be used. Otherwise,
 * the same prefix as in development will be used.
 */
var getUrlPrefix = function() {
  if (production() && config.hasOwnProperty('urlPrefix') && config.urlPrefix) {
    return config.urlPrefix;
  }

  var prefix = config.buildDir.replace(path.join(__dirname, 'web'), '');
  while (prefix.charAt(0) === path.sep) {
    prefix = prefix.substring(1);
  }

  return '/' + prefix;
};

/**
 * Utility function that calls gulp-main-bower-files while filtering the error
 * it throws if the bower_components/ directory does not exist.
 */
var bower = function() {
  return require('gulp-main-bower-files')({
    bowerJson: config.vendor.bowerFile,
    bowerDirectory: config.vendor.bowerComponents
  }, function(error) {
    if (error && !error.toString().match(/^Error: Bower components directory does not exist/)) {
      util.log(error);
    }
  });
};
