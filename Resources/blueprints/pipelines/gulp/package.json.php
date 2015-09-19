{
  "main": "gulpfile.js",
  "description": "<?php echo $projectName ?>",
  "repository": "<?php echo $projectName ?>",
  "license": "MIT",
  "devDependencies": {
    "del": "^1.2.1",
    "gulp": "^3.9.0",
    "gulp-add-src": "^0.2.0",
    "gulp-babel": "^5.2.1",
    "gulp-cached": "^1.1.0",
<?php if ($coffee) { ?>
    "gulp-coffee": "^2.3.1",
<?php } ?>
    "gulp-concat": "^2.6.0",
    "gulp-debug": "^2.1.0",
    "gulp-environments": "^0.1.1",
    "gulp-filter": "^3.0.1",
    "gulp-flatten": "^0.2.0",
<?php if ($cssPre === 'less') { ?>
    "gulp-less": "^3.0.3",
<?php } ?>
    "gulp-livereload": "^3.8.0",
    "gulp-main-bower-files": "^1.2.1",
    "gulp-minify-css": "^1.2.1",
    "gulp-replace": "^0.5.4",
    "gulp-rev-all": "^0.8.21",
<?php if ($cssPre === 'sass') { ?>
    "gulp-sass": "^2.0.4",
<?php } ?>
    "gulp-uglify": "^1.2.0",
    "gulp-util": "^3.0.6"
  }
}
