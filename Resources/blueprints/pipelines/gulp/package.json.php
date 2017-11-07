{
  "main": "gulpfile.js",
  "description": "<?php echo $projectName ?>",
  "repository": "<?php echo $projectName ?>",
  "license": "MIT",
  "devDependencies": {
    "babel-core": "^6.26.0",
    "del": "^3.0.0",
    "gulp": "^3.9.0",
    "gulp-add-src": "^0.2.0",
    "gulp-babel": "^7.0.0",
    "gulp-cached": "^1.1.0",
    "gulp-clean-css": "^3.9.0",
<?php if ($coffee) { ?>
    "gulp-coffee": "^2.3.1",
<?php } ?>
    "gulp-concat": "^2.6.0",
    "gulp-debug": "^3.1.0",
    "gulp-environments": "^0.1.1",
    "gulp-filter": "^5.0.1",
    "gulp-flatten": "^0.3.1",
<?php if ($cssPre === 'less') { ?>
    "gulp-less": "^3.3.2",
<?php } ?>
    "gulp-livereload": "^3.8.0",
    "gulp-main-bower-files": "^1.6.2",
    "gulp-replace": "^0.6.1",
    "gulp-rev-all": "^0.9.7",
<?php if ($cssPre === 'sass') { ?>
    "gulp-sass": "^3.1.0",
<?php } ?>
    "gulp-uglify": "^3.0.0",
    "gulp-util": "^3.0.8"
  }
}
