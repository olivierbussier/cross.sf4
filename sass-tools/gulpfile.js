// Requis
var gulp = require('gulp');

// Include plugins
var plugins = require('gulp-load-plugins')(); // tous les plugins de package.json

// Variables de chemins
var source = '../sass'; // dossier de travail
var destination = '../public/css'; // dossier à livrer

gulp.task('autoprefix', function () {
  return gulp.src(destination + '/*.css')
    .pipe(plugins.csscomb())
    .pipe(plugins.cssbeautify({indent: '  '}))
    .pipe(plugins.autoprefixer())
    .pipe(gulp.dest(destination));
});

gulp.task('minify', function () {
  return gulp.src(destination + '/*.css')
    .pipe(plugins.csso())
    .pipe(plugins.rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest(destination + '/min/'));
});

gulp.task('min', function () {
  
  return gulp.src([destination + '/*.css','!' + destination + '/*.min.*'])
    .pipe(plugins.csso())
    .pipe(plugins.rename({suffix: '.min'}))
    .pipe(gulp.dest(destination       ))
});
