Vue.filter('translate', function (value, path) {
    let translation = value[Spark.locale];
    return path == undefined ? translation : `/${path}/${translation}`;
});

// Inits twemoji
if (typeof twemoji == 'object') {
    twemoji.parse(document);
}