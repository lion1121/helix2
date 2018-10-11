'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('sass', function () {
    return gulp.src('./resourses/assets/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('public/css'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./resourses/assets/sass/**/*.scss', ['sass']);
    gulp.watch('./resourses/assets/sass/**/**/*.scss', ['sass']);
});