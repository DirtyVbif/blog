// =====================================================================
// project settings
const src = {
    assets: {
        css: 'assets/css/**/*.+(scss|sass)',
        js: 'assets/js/*.js',
        jsc: 'assets/js/classes/**/*.js',
        lib_js: 'assets/libraries/**/js/*.js',
        lib_css: 'assets/libraries/**/css/**/*.+(scss|sass)',
        img: 'assets/img/**/*.+(png|jpg|webp|tiff)'
    },
    dest: {
        pub: 'public/',
        lib: 'libraries/',
        img: 'public/images/'
    }
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
    },
    lib: {
        base: 'assets/libraries/',
        sourcemap: true
    },
    img: {
        base: 'assets/img/',
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
addHeader = require('gulp-header'),                     // add first line into file
// addFooter = require('gulp-footer');                  // add lasst line into file
webp = require('gulp-webp');                            // converting images into *.webp format

// =====================================================================
//clean target directories
function clean() {
    return delFiles([
        src.dest.pub + 'css/',
        src.dest.pub + 'js/'
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
        .pipe(gulp.dest(src.dest.pub));
}

function css_lib() {
    return gulp.src(src.assets.lib_css, opt.lib)
        .pipe(
            sass(opt.css)
                .on('error', sass.logError)
        ).pipe(autoPrefixer())
        .pipe(cssMin())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(src.dest.lib));
}

// =====================================================================
// watch css changes
function watch_css() {
    return gulp.watch(src.assets.css, css);
}

function watch_css_lib() {
    return gulp.watch(src.assets.lib_css, css_lib);
}

// =====================================================================
// minifine and put main.js scripts into target directories
function js() {
    return gulp.src(src.assets.js, opt.src)
        .pipe(addHeader('"use strict";'))
        .pipe(minifyJS())
        .pipe(
            rename({ suffix: '.min' })
        ).pipe(gulp.dest(src.dest.pub));
}

function jsc() {
    return gulp.src(src.assets.jsc)
        .pipe(concat('classes.js'))
        .pipe(addHeader('"use strict";'))
        .pipe(minifyJS())
        .pipe(
            rename({ suffix: '.min' })
        ).pipe(gulp.dest(src.dest.pub + 'js/'));
}

function js_lib() {
    return gulp.src(src.assets.lib_js, { base: 'assets/libraries/', sourcemap: true })
        // .pipe(concat('script.js'))
        // .pipe(addHeader('"use strict";'))
        .pipe(minifyJS())
        .pipe(
            rename({ suffix: '.min' })
        ).pipe(gulp.dest(src.dest.lib));
}

// =====================================================================
// watch js changes
function watch_js() {
    return gulp.watch(src.assets.js, js);
}

function watch_jsc() {
    return gulp.watch(src.assets.jsc, jsc);
}

function watch_js_lib() {
    return gulp.watch(src.assets.lib_js, js_lib);
}

// =====================================================================
// converting images into webp
function img() {
    return gulp.src(src.assets.img, opt.img)
        .pipe(webp({
            quality: 100,
            method: 6
        }))
        .pipe(gulp.dest(src.dest.img));
}

function watch_img() {
    return gulp.watch(src.assets.img, img);
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
        css, css_lib, js, jsc, js_lib, img
    ),
    gulp.parallel(
        watch_css, watch_css_lib, watch_js, watch_jsc, watch_js_lib, watch_img
    )
);
