const gulp = require('gulp');
const concat = require('gulp-concat');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const del = require('del');
const browserSync = require('browser-sync').create();
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const plumber = require('gulp-plumber');
const notify = require("gulp-notify");
const fileinclude = require('gulp-file-include');
const tiny = require('gulp-tinypng-nokey');
const replace = require('gulp-replace');
const htmlmin = require('gulp-htmlmin');

function styles() {
   return gulp.src(
      [
         './src/scss/style.scss',
         './src/scss/rtl.scss',
         './src/scss/style-skin-snowboards.scss',
         './src/scss/style-skin-phones.scss',
         './src/scss/style-skin-lingerie.scss',
         './src/scss/style-skin-furniture.scss',
         './src/scss/style-skin-carsshop.scss',
         './src/scss/style-skin-books.scss',
         './src/scss/style-skin-bikes.scss',
         //wookie 2
         './src/scss/style-skin-baby.scss',
         './src/scss/style-skin-food.scss',
         './src/scss/style-skin-food02.scss',
         './src/scss/style-skin-oneproducts.scss',
         './src/scss/style-skin-oneproducts02.scss',
         './src/scss/style-skin-oneproducts03.scss',
         './src/scss/style-skin-coffee.scss',
         './src/scss/style-skin-clothes.scss',
         './src/scss/index-skin-cosmetics.scss',
         './src/scss/style-skin-electronics.scss',
         './src/scss/style-skin-glasses.scss',
         './src/scss/style-skin-handmade.scss',
         './src/scss/style-skin-shirts.scss',
         './src/scss/style-skin-cookware.scss',
         './src/scss/style-skin-cakes.scss',
         './src/scss/style-skin-christmas.scss',
         './src/scss/style-skin-flowers.scss',
         './src/scss/style-skin-furniture02.scss',
         './src/scss/style-skin-gothic.scss',
         './src/scss/style-skin-jewerly.scss',
         './src/scss/style-skin-kids-clothes.scss',
         './src/scss/style-skin-watches.scss',
         './src/scss/style-skin-care.scss',
         './src/scss/style-skin-beer.scss',
         './src/scss/style-skin-books02.scss',
         './src/scss/style-skin-bicycle.scss',
         './src/scss/style-skin-tools.scss',
         './src/scss/style-skin-toys.scss',
         './src/scss/style-skin-tea.scss',
         './src/scss/style-skin-comic-books.scss',
         './src/scss/style-skin-wallets.scss',
         './src/scss/style-skin-weapons.scss',
         './src/scss/style-skin-medical.scss',
         './src/scss/style-skin-phone-cases.scss',
         './src/scss/style-skin-yoga.scss',
         './src/scss/style-skin-plants.scss',
         './src/scss/style-skin-basketball.scss',
         './src/scss/style-skin-football.scss',
         './src/scss/style-skin-base-ball.scss',
         './src/scss/style-skin-lifestyle.scss',
         './src/scss/style-skin-drones.scss',
         './src/scss/style-skin-olivers.scss'


      ]
   )
   .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
   .pipe(sass().on('error', sass.logError))
   .pipe(autoprefixer({
      overrideBrowserslist: ['last 2 versions'],
	  cascade: false
   }))
   // .pipe(cleanCSS({
   //    level: 2
   // }))
   .pipe(gulp.dest('./build/css'))
   .pipe(gulp.dest('./src/css'))
   .pipe(browserSync.stream());
}
const jsFiles = [
   './src/external/bootstrap/js/bootstrap.min.js',
   './src/external/slick/slick.min.js',
   './src/external/elevatezoom/jquery.elevatezoom.js',
   './src/external/isotope/imagesloaded.js',
   './src/external/isotope/isotope.pkgd.min.js',
   './src/external/magnific-popup/jquery.magnific-popup.min.js',
   './src/external/perfect-scrollbar/perfect-scrollbar.min.js',
   './src/external/panelmenu/panelmenu.js',
   './src/external/instagram-feed/jquery.instagramFeed.min.js',
   './src/external/rs-plugin/js/jquery.themepunch.tools.min.js',
   './src/external/rs-plugin/js/jquery.themepunch.revolution.min.js',
   './src/external/countdown/jquery.plugin.min.js',
   './src/external/countdown/jquery.countdown.min.js',
   './src/external/lazyLoad/lazyload.min.js',
   './src/external/form/jquery.form.js',
   './src/external/form/jquery.validate.min.js',
   './src/external/form/jquery.form-init.js',
   './src/js/**/*.js'
];

function scripts() {
   return gulp.src(jsFiles,  { allowEmpty: true })
   .pipe(concat('bundle.js'))
   // .pipe(uglify({
   //    toplevel: true
   // }))
   .pipe(gulp.dest('./build/js'))
   .pipe(browserSync.stream());
}
function clean() {
   return del(['build/*'])
}
function html() {
   return gulp.src('./src/*.html')
   .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
   .pipe(fileinclude({
	  prefix: '@@',
	  basepath: '@file'
	}))
   .pipe(gulp.dest('./build/'))
   .pipe(browserSync.stream());
}
function favicon() {
   return gulp.src('./src/favicon.ico')
   .pipe(gulp.dest('./build/'))
   .pipe(browserSync.stream());
}
function external() {
   return gulp.src('./src/external/**/*')
   .pipe(gulp.dest('./build/external/'))
   .pipe(browserSync.stream());
}
function separateInclude() {
   return gulp.src('./src/separate-include/**/*')
   .pipe(gulp.dest('./build/separate-include/'))
   .pipe(browserSync.stream());
}
function font_icons() {
   return gulp.src('./src/font/**/*')
   .pipe(gulp.dest('./build/font/'))
   .pipe(browserSync.stream());
}
function video() {
   return gulp.src('./src/video/*')
   .pipe(gulp.dest('./build/video/'))
   .pipe(browserSync.stream());
}
function sound() {
   return gulp.src('./src/sound/*')
   .pipe(gulp.dest('./build/sound/'))
   .pipe(browserSync.stream());
}
function scss() {
   return gulp.src('./src/scss/*')
   .pipe(gulp.dest('./build/scss/'))
   .pipe(browserSync.stream());
}
function ajaxContent() {
   return gulp.src('./src/ajax-content/*')
   .pipe(gulp.dest('./build/ajax-content/'))
   .pipe(browserSync.stream());
}
function img() {
   return gulp.src('./src/images/**/*')
  // .pipe(tiny())
   .pipe(gulp.dest('./build/images/'))
   .pipe(browserSync.stream());
}
function watch() {
   browserSync.init({
	  server: {
		  baseDir: "./build/"
	  }
  });
  gulp.watch('./src/scss/**/*.scss', styles)
  gulp.watch('./src/js/**/*.js', scripts)
  gulp.watch('./src/**/*.html', gulp.series(html)).on('change', browserSync.reload);
}

gulp.task('styles', styles);
gulp.task('html', html);
gulp.task('scripts', scripts);
gulp.task('del', clean);
gulp.task('img', img);
gulp.task('external', external);
gulp.task('font_icons', font_icons);
gulp.task('favicon', favicon);
gulp.task('ajaxContent', ajaxContent);
gulp.task('watch', watch);
gulp.task('build', gulp.series(clean, gulp.parallel(styles,favicon,external,separateInclude,font_icons,video,sound,scss,ajaxContent,img,scripts,html)));


gulp.task('html-minify', () => {
   return gulp.src('./build/*.html')
     .pipe(htmlmin({ collapseWhitespace: true }))
     .pipe(gulp.dest('./build/'));
 });


gulp.task('dev', gulp.series('build','watch'));