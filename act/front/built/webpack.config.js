const webpack = require('webpack');
const path = require('path');
const AssetsPlugin = require('assets-webpack-plugin');
const CleanPlugin = require('clean-webpack-plugin');
const app = {
    context: __dirname + '/modules',
    stats: {
        warnings: false
    },
    entry: {
        'apps': [
            path.resolve(__dirname, 'pages', 'OrderPage.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ]
    },
    //target: 'node',
    output: {
        filename: '[name].[chunkhash].js',
        path: path.resolve(__dirname, '../public/dist'),
        publicPath: '../public/dist/'
    },
    resolve: {
        extensions: ['.ts', '.js'],
        alias: {
            module1: path.resolve(__dirname, 'modules/order/'),
            "jquery-ui": "jquery-ui/jquery-ui.js",
            // bind to modules;
            modules: path.join(__dirname, "node_modules"),
        }
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                exclude: /(node_modules)/,
                loaders: [
                    {
                        loader: 'ts-loader',
                        options: {
                            transpileOnly: true,
                            experimentalWatchApi: true
                        },
                    }, 'angular2-template-loader'
                ]
            },
            {
                test: /\.(gif|svg|jpg|png|ttf|woff|eot)$/,
                loader: "file-loader",
                options: {
                    publicPath: 'dist'
                }
            },
            {
                test: /\.html$/,
                loader: 'raw-loader'
            },
            {
                test: /\.css$/,
                exclude: [
                    path.resolve(__dirname, "node_modules/primeng"),
                    path.resolve(__dirname, 'node_modules/primeicons'),
                ],
                loaders: ['to-string-loader', 'css-loader']
            },
            {
                test: /\.css$/,
                include: [
                    path.resolve(__dirname, "node_modules/primeng"),
                    path.resolve(__dirname, 'node_modules/primeicons'),
                ],
                loaders: ['style-loader', 'css-loader']
            }
        ]
    },
    plugins: [
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: JSON.stringify('production'),
                APP_ENV: JSON.stringify('browser')
            }
        }),
        new AssetsPlugin({
            fileName: 'webpack.assets.json',
            path: path.resolve(__dirname, 'dist'),
            cleanAfterEveryBuildPatterns: ['dist']
        }),
        new CleanPlugin([
            path.resolve(path.resolve(__dirname, '../public/dist'), '*.js'),
            path.resolve(path.resolve(__dirname, '../public/dist'), '*.js.map')
        ])
    ]
};
app.plugins.push(function () {
    this.plugin('afterCompile', function (stats) {
        console.log(('\n\n\nNouvelle compilation : [' + new Date().toLocaleTimeString("fr") + ']'));
    });
});
module.exports = (env, options) => {
    if (options && options.mode && options.mode === 'development') {
        //no need to hash output in development!
        app.output.filename = '[name].js';
        app.watch = true;
    }
    return app;
};
