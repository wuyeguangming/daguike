// 用法:
// gulp         编译源码
// gulp build   编译dist
var del       = require('del');                 //删除之前的版本
var gulp      = require('gulp');
var jade      = require('gulp-jade');
var coffee    = require('gulp-coffee');
var sass      = require('gulp-sass');
var ngHtml2Js = require("gulp-ng-html2js");     //angularjs模板缓存
var concat    = require('gulp-concat');         //连接合并
var uglify    = require('gulp-uglify');         //优化压缩
var jshint    = require("gulp-jshint");         //js语法检查等
// var insert    = require('gulp-insert');
var rename    = require("gulp-rename");         //重命名
var minifycss = require('gulp-minify-css');     //压缩
// var manifest  = require('gulp-asset-manifest'); //添加/更新asset_manifest.json，模板由此读取assets文件名
var hash      = require('gulp-rev');            //添加hash值: a.js -> a-xxx.js
var notify    = require("gulp-notify");         //错误通知
var order     = require("gulp-order");          //确保__init__在前面
var composer = require('gulp-composer');        // 运行composer命令
var mainBowerFiles = require('main-bower-files');
var filter = require('gulp-filter');
var flatten = require('gulp-flatten');
var hashDel = require('daguike-gulp-rev-del');
var merge = require('merge-stream');
// var run = require('gulp-run');

var fs = require('fs');
var log = console.log;
var path = {
    bower_components: './bower_components',
    src:{
        base: './app/assets',
        js: './app/assets/coffee',
        css: './app/assets/sass',
        jade: './app/assets/jade',
        fonts: './app/assets/fonts'
    },
    dev:{
        base: './public_html/dev',
        js: './public_html/dev/js',
        tpl: './public_html/dev/tpl',
        css: './public_html/dev/css',
        fonts: './public_html/dev/fonts'
    },
    dist:{
        base: './public_html/temp',
        js: './public_html/temp/js',
        tpl: './public_html/temp/tpl',
        css: './public_html/temp/css',
        fonts: './public_html/temp/fonts'
    }
};
var buildPath = {};

// 替换为开发路径
function replace2dev (dest) {
    return dest.replace(path.dist.js,path.dev.js)
        .replace(path.dist.css,path.dev.css)
        .replace(path.dist.base,path.dev.base)
        .replace(path.src.js,path.dev.js)
        .replace(path.src.css,path.dev.css)
        .replace(path.src.base,path.dev.base)
}
// 替换为发布路径
function replace2dist (dest) {
    return dest.replace(path.src.js,path.dist.js)
        .replace(path.src.css,path.dist.css)
        .replace(path.src.base,path.dist.base)
        .replace(path.dev.js,path.dist.js)
        .replace(path.dev.css,path.dist.css)
        .replace(path.dev.base,path.dist.base)
}

// 遍历目录，提取group和module的名称
function scan(path){
    var modules = [],groups = [],walk = function(path, modules, groups, is_group){
        files = fs.readdirSync(path);
        files.forEach(function(item) {
            var tmpPath = path + '/' + item,stats = fs.statSync(tmpPath);
            if (stats.isDirectory()) {
                if (is_group) {
                    groups.push(tmpPath);
                    walk(tmpPath, modules, groups, false);
                }else{
                    modules.push(tmpPath);
                }
            }
        });
    };
    walk(path, modules, groups, true);
    return {'groups' : groups,'modules' : modules}
}

gulp.task("jade", function() {
    return gulp.src(path.src.jade+'/**/**/*.jade')
        .pipe(jade({pretty: false}))
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(ngHtml2Js({moduleName: 'tpl'}))//todo rename
        .pipe(rename(function (path) {
            path.extname = '.tpl.js'
        }))
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(hash())
        .pipe(gulp.dest(path.dev.tpl))
        .pipe(hash.manifest())
        .pipe(hashDel({dest: path.dev.tpl}))
        .pipe(gulp.dest(path.dev.tpl));
});

gulp.task('libs', function () {
    // js
    var paths = scan(path.src.js);
    var groups = paths.groups;
    var modules = paths.modules;
    var stream, stream_temp;
    for (var i = 0; i < modules.length; i++) {
        if (fs.existsSync(modules[i]+'/libs.json')) {
            var module = modules[i];
            var buf = fs.readFileSync(modules[i]+'/libs.json');
            var libs = JSON.parse(String(buf));
            for (var j = 0; j < libs.path.length; j++) {
                libs.path[j] = path.bower_components+'/'+libs.path[j]
            }
            stream_temp = gulp.src(libs.path)
                .pipe(concat('libs.js'))
                .pipe(hash())
                .pipe(uglify())
                .pipe(gulp.dest(replace2dev(module)))
                .pipe(hash.manifest())
                .pipe(hashDel({dest: replace2dev(module)}))
                .on("error", notify.onError("<%= error.message %>"))
                .pipe(gulp.dest(replace2dev(module)))
            if (stream) {
                stream = merge(stream, stream_temp);
            } else {
                stream = stream_temp;
            }
        }
    };

    // css
    paths = scan(path.src.css);
    groups = paths.groups;
    modules = paths.modules;
    for (var i = 0; i < modules.length; i++) {
        if (fs.existsSync(modules[i]+'/libs.json')) {
            var module = modules[i];
            var buf = fs.readFileSync(modules[i]+'/libs.json')
            var libs = JSON.parse(String(buf));
            for (var j = 0; j < libs.path.length; j++) {
                libs.path[j] = path.bower_components+'/'+libs.path[j]
            };
            stream_temp = gulp.src(libs.path)
                .pipe(concat('libs.css'))
                .pipe(hash())
                .pipe(gulp.dest(replace2dev(module)))
                .pipe(hash.manifest())
                .pipe(hashDel({dest: replace2dev(module)}))
                .on("error", notify.onError("<%= error.message %>"))
                .pipe(gulp.dest(replace2dev(module)));
            if (stream) {
                stream = merge(stream, stream_temp);
            } else {
                stream = stream_temp;
            }
        }
    }
    return stream;
});

gulp.task("js", function() {
    return gulp.src(path.src.js+'/**/**/*.coffee')
        .pipe(coffee({bare: true}))
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(hash())
        .pipe(gulp.dest(path.dev.js))
        .pipe(hash.manifest())
        .pipe(hashDel({dest: path.dev.js}))
        .pipe(gulp.dest(path.dev.js));
});

gulp.task("css", function() {
    return gulp.src(path.src.css+'/**/**/*.scss')
        .pipe(sass())
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(hash())
        .pipe(gulp.dest(path.dev.css))
        .pipe(hash.manifest())
        .pipe(hashDel({dest: path.dev.css}))
        .pipe(gulp.dest(path.dev.css));
});

gulp.task("fonts", function() {
    var stream1 = gulp.src(mainBowerFiles())
        .pipe(filter('**/*.{eot,svg,ttf,woff,otf}'))
        .pipe(flatten())
        .pipe(gulp.dest(path.dev.fonts));

    var stream2 = gulp.src(path.src.fonts+'/**/*')
        .pipe(filter('**/*.{eot,svg,ttf,woff,otf}'))
        .pipe(flatten())
        .pipe(gulp.dest(path.dev.fonts));

    return merge(stream1, stream2);
});

// composer
gulp.task("autoload", function() {
    composer('dumpautoload',{});
});
gulp.task("autoload_opt", function() {
    composer('dumpautoload', {optimize: true});
});

gulp.task("build_fonts", ['build_dist'], function() {
    var stream1 = gulp.src(mainBowerFiles())
        .pipe(filter('**/*.{eot,svg,ttf,woff,otf}'))
        .pipe(flatten())
        .pipe(gulp.dest(path.dist.fonts));

    var stream2 = gulp.src(path.src.fonts+'/**/*')
        .pipe(filter('**/*.{eot,svg,ttf,woff,otf}'))
        .pipe(flatten())
        .pipe(gulp.dest(path.dist.fonts));

    return merge(stream1, stream2);
});

gulp.task('build_js', ['build_dist'], function() {
    // js
    var paths = scan(path.dev.js);
    var groups = paths.groups;
    var modules = paths.modules;
    var stream, stream_temp;
    for (var i = 0; i < modules.length; i++) {
        stream_temp = gulp.src(modules[i]+'/*.js')
        .pipe(order([]))
        // .pipe(jshint())
        .pipe(uglify())
        .pipe(concat('all.js'))
        .pipe(hash())
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(gulp.dest(replace2dist(modules[i])));
        if (stream) {
            stream = merge(stream, stream_temp);
        } else {
            stream = stream_temp;
        }
    }
    return stream;
});

gulp.task('build_css', ['build_dist'], function() {
    // css
    var paths = scan(path.dev.css);
    var groups = paths.groups;
    var modules = paths.modules;
    var stream, stream_temp;
    for (var i = 0; i < modules.length; i++) {
        stream_temp = gulp.src(modules[i]+'/*.css')
        .pipe(minifycss())
        .pipe(concat('all.css'))
        .pipe(hash())
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(gulp.dest(replace2dist(modules[i])));
        if (stream) {
            stream = merge(stream, stream_temp);
        } else {
            stream = stream_temp;
        }
    }
    return stream;
});

gulp.task('build_tpl', ['build_dist'], function() {
    var paths = scan(path.dev.tpl);
    var groups = paths.groups;
    var modules = paths.modules;
    var stream, stream_temp;
    for (var i = 0; i < modules.length; i++) {
        stream_temp = gulp.src(modules[i]+'/*.js')
        .pipe(order([]))
        // .pipe(jshint())
        .pipe(uglify())
        .pipe(concat('all.js'))
        .pipe(hash())
        .on("error", notify.onError("<%= error.message %>"))
        .pipe(gulp.dest(replace2dist(modules[i])));
        if (stream) {
            stream = merge(stream, stream_temp);
        } else {
            stream = stream_temp;
        }
    }
    return stream;
});

gulp.task('clean_temp', function (cb) {
    del('public_html/temp/', cb);
});

gulp.task('build_dist', ['clean_temp', 'jade', 'libs', 'js', 'css']);

gulp.task('clean_dist',['build_fonts', 'build_js', 'build_css', 'build_tpl'], function (cb) {
    del('public_html/dist/', cb);
});

gulp.task('move_files', ['clean_dist'], function () {
    return gulp.src('public_html/temp/**')
      .pipe(gulp.dest('public_html/dist/'));
});

gulp.task('delete_files', ['move_files'], function (cb) {
    del(['public_html/dev/', 'public_html/temp/'], cb);
});

gulp.task('build', ['delete_files']);

gulp.task('build_dev', ['fonts', 'jade', 'libs', 'js', 'css']);
gulp.task('watch', ['build_dev'],function(){
    gulp.watch(path.src.jade+'/**/**/*.jade', ['jade']);
    gulp.watch(path.src.js+'/**/**/*.coffee',   ['js']);
    gulp.watch(path.src.css+'/**/**/*.scss',  ['css']);
    gulp.watch(path.src.base+'/**/**/**/libs.json',  ['libs']);
});
gulp.task('default', ['watch']);
