const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const browserSync = require('browser-sync').create();
function errorlog(err) {
    console.error(err.message);
    this.emit('end');
}

function style() {
    return gulp.src('scss/style.scss').pipe(sass().on('error', errorlog)).pipe(autoprefixer()).pipe(sourcemaps.write()).pipe(gulp.dest('./')).pipe(browserSync.stream());
}

function watch() {
    browserSync.init({
        proxy: 'https://sapirjournal-v2.local',
    });

    gulp.watch('./scss/**/*.scss', style);

    gulp.watch('./*.php').on('change', browserSync.reload);
    gulp.watch('./templates/**/*.php').on('change', browserSync.reload);
    gulp.watch('./template-parts/**/*.php').on('change', browserSync.reload);
    gulp.watch('./blocks/**/*.php').on('change', browserSync.reload);

    gulp.watch('./js/**/*.js').on('change', browserSync.reload);
}

exports.style = style;
exports.watch = watch;
