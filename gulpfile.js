// =====================================================================
// project settings
const src = {
    assets: {
        css: 'assets/css/**/*.+(scss|sass)',
        js: 'assets/js/*.js',
        jsc: 'assets/js/classes/**/*.js'
    },
    dest: 'public/'
},
opt = {
    css: {
        // "sourcemap=none": true,
        // noCache: true,
        // compass: true,
        // style: sassStyle,
        // lineNumbers: false,
        outputStyle: 'expanded'
    },
    src:  {
        base: 'assets/',
        sourcemap: true
    }
},
// =====================================================================
// gulp packages
gulp = require('gulp'),                                 // gulp
sass = require('gulp-sass')(require('sass')),           // sass compiler
minifyJS = require('gulp-terser'),                      // minification JS
autoPrefixer = require('gulp-autoprefixer'),            // css autoprefixer
rename = require('gulp-rename'),                        // rename outputs files
delFiles = require('del'),                              // files delete
cssMin = require('gulp-csso');                          // minification css
concat = require('gulp-concat'),                        // implode files into one
addHeader = require('gulp-header');                     // add first line into file
// addFooter = require('gulp-footer');                  // add lasst line into file

// =====================================================================
//clean target directories
function clean() {
    return delFiles([
        src.dest + 'css/',
        src.dest + 'js/'
    ], {
        force: true
    });
}

// =====================================================================
// compile SASS/SCSS files into target css public directory
function css() {
    return gulp.src(src.assets.css, opt.src)
        .pipe(
            sass(opt.css)
                .on('error', sass.logError)
        ).pipe(autoPrefixer())
        .pipe(cssMin())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(src.dest));
}

// =====================================================================
// watch css changes
function watch_css() {
    return gulp.watch(src.assets.css, css);
}

// =====================================================================
// minifine and put main.js scripts into target directories
function js() {
    return gulp.src(src.assets.js, opt.src)
        .pipe(addHeader('"use strict";'))
        .pipe(minifyJS())
        .pipe(
            rename({ suffix: '.min' })
        ).pipe(gulp.dest(src.dest + '/'));
}

function jsc() {
    return gulp.src(src.assets.jsc)
        .pipe(concat('classes.js'))
        .pipe(addHeader('"use strict";'))
        .pipe(minifyJS())
        .pipe(
            rename({ suffix: '.min' })
        ).pipe(gulp.dest(src.dest + 'js/'));
}

// =====================================================================
// watch js changes
function watch_js() {
    return gulp.watch(src.assets.js, js);
}

function watch_jsc() {
    return gulp.watch(src.assets.jsc, jsc);
}

// =====================================================================
// output jQuery lib
// gulp.task('js_lib', () => {
//     return gulp.src(src.lib.js)
//         .pipe(gulp.dest(src.dir.js));
// });

// =====================================================================
// BUILD TASKS
exports.default = gulp.series(
    clean,
    gulp.parallel(
        css, js, jsc,
    ),
    gulp.parallel(
        watch_css, watch_js, watch_jsc
    )
);
