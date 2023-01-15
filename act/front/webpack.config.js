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
        ],
        'display-otp': [
            path.resolve(__dirname, 'pages', 'ValidationAndSignatureForOtpUserPage.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'signature-otp': [
            path.resolve(__dirname, 'pages', 'SignatureForOtpUserPage.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'liste-contact': [
            path.resolve(__dirname, 'pages', 'ListeContactPage.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'informations': [
            path.resolve(__dirname, 'pages', 'InformationsPage.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'faq': [
            path.resolve(__dirname, 'pages', 'FaqPage.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'download-otp': [
            path.resolve(__dirname, 'pages', 'DownloadOtp.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'dashboard': [
            path.resolve(__dirname, 'pages', 'Dashboard.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ],
        'dashboard-counsel': [
            path.resolve(__dirname, 'pages', 'DashboardCounsel.ts'),
            path.resolve(__dirname, 'node_modules/primeng/resources', 'primeng.min.css'),
            path.resolve(__dirname, 'node_modules/primeicons', 'primeicons.css'),
        ]
    },
    output: {
        filename: '[name].[chunkhash].js',
        path: path.resolve(__dirname, '../public/dist'),
        publicPath: '../public/dist/',
        pathinfo: false,
        crossOriginLoading: false
    },
    resolve: {
        extensions: ['.ts', '.js'],
        alias: {
            module1: path.resolve(__dirname, 'modules/order/'),
            module2: path.resolve(__dirname, 'modules/ValidationAndSignatureForOtpUser/'),
            module3: path.resolve(__dirname, 'modules/SignatureForOtpUser/'),
            module4: path.resolve(__dirname, 'modules/contact/'),
            module5: path.resolve(__dirname, 'modules/informations'),
            module6: path.resolve(__dirname, 'modules/faq'),
            module7: path.resolve(__dirname, 'modules/DownloadDocumentForOTP'),
            module8: path.resolve(__dirname, 'modules/Dashboard'),
            module9: path.resolve(__dirname, 'modules/DashboardCounsel'),
            "jquery-ui": "jquery-ui/jquery-ui.js",
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
    optimization: {
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
        new CleanPlugin(['dist'], {
            root: 'C:/Users/bejao/newgit/acte_sous_signature_privee/public',
            verbose: true,
            dry: false
        })
    ]
};

app.plugins.push(function () {
    this.plugin('afterCompile', function (stats) {
        console.log(('\n\n\nNouvelle compilation : [' + new Date().toLocaleTimeString("fr") + ']'));
    });
});

module.exports = (env, options) => {
    if (options && options.mode && options.mode === 'development') {
        app.output.filename = '[name].js';
        app.watch = true;
    }
    if (options && options.mode && options.mode === 'production') {
        app.output.filename = '[name].js';
    }

    return app;
};