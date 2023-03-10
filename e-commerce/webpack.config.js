// Modules
var path                  = require("path");
var webpack               = require('webpack');
var MiniCssExtractPlugin  = require("mini-css-extract-plugin");
var CopyWebpackPlugin     = require("copy-webpack-plugin");
var {CleanWebpackPlugin}  = require("clean-webpack-plugin");
var WebpackAssetsManifest = require('webpack-assets-manifest');
var Encore = require('@symfony/webpack-encore');

var projectRoot           = path.resolve(__dirname,);
var nodeModulesRoot       = path.join(projectRoot, 'node_modules');
var pathOutput            = path.join(projectRoot, '/public/build');
Encore
    // ...
    .copyFiles([
        {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/vendor', to: 'ckeditor/vendor/[path][name].[ext]'}
    ])
// Uncomment the following line if you are using Webpack Encore <= 0.24
// .addLoader({test: /\.json$/i, include: [require('path').resolve(__dirname, 'node_modules/ckeditor')], loader: 'raw-loader', type: 'javascript/auto'})
;
// Config
module.exports = {
    mode: 'production',
    cache : true,

    entry: {
        // Admin
        admin: [
            path.join(projectRoot, 'assets/backadmin/css/loader.css'),
            path.join(projectRoot, 'assets/backadmin/bootstrap/css/bootstrap.min.css'),
            path.join(projectRoot, 'assets/backadmin/css/plugins.css'),
            path.join(projectRoot, 'assets/backadmin/css/elements/miscellaneous.css'),
            path.join(projectRoot, 'assets/backadmin/css/elements/breadcrumb.css'),
            path.join(projectRoot, 'assets/backadmin/plugins/apex/apexcharts.css'),
            path.join(projectRoot, 'assets/backadmin/css/dashboard/dash_1.css'),
            path.join(projectRoot, 'assets/backadmin/plugins/select2/select2.min.css'),
            path.join(projectRoot, 'assets/backadmin/css/forms/theme-checkbox-radio.css'),
            path.join(projectRoot, 'assets/backadmin/css/tables/table-basic.css'),
            path.join(projectRoot, 'assets/backadmin/plugins/table/datatable/datatables.css'),
            path.join(projectRoot, 'assets/backadmin/plugins/table/datatable/dt-global_style.css'),
            path.join(projectRoot, 'assets/backadmin/css/dashboard/dash_2.css'),
            path.join(projectRoot, 'assets/backadmin/css/elements/infobox.css'),
            path.join(projectRoot, 'assets/backadmin/plugins/file-upload/file-upload-with-preview.min.css'),
            path.join(projectRoot, 'assets/backadmin/css/components/cards/card.css'),
            path.join(projectRoot, 'assets/backadmin/css/scrollspyNav.css'),
            path.join(projectRoot, 'assets/backadmin/css/components/custom-carousel.css'),
            path.join(projectRoot, 'assets/backadmin/css/authentication/form-1.css'),
            path.join(projectRoot, 'assets/backadmin/css/style.css'),
            path.join(projectRoot, 'assets/backadmin/plugins/blockui/jquery.blockUI.min.js'),
            path.join(projectRoot, 'assets/backadmin/js/authentication/form-1.js'),
            path.join(projectRoot, 'assets/backadmin/plugins/perfect-scrollbar/perfect-scrollbar.min.js'),
            path.join(projectRoot, 'assets/backadmin/loader.js'),
            path.join(projectRoot, 'assets/backadmin/plugins/highlight/highlight.pack.js'),
            path.join(projectRoot, 'assets/backadmin/custom.js'),
            path.join(projectRoot, 'assets/backadmin/plugins/file-upload/file-upload-with-preview.min.js'),
            path.join(projectRoot, 'assets/backadmin/plugins/select2/select2.min.js'),
            path.join(projectRoot, 'assets/backadmin/plugins/select2/custom-select2.js'),
            path.join(projectRoot, 'assets/backadmin/scrollspyNav.js'),
        ],
        // Site
        site: [
            path.join(projectRoot, 'assets/front/css/theme.min.css'),
            // path.join(projectRoot, 'assets/frontTow/css/bootstrap.min.css'),
            path.join(projectRoot, 'assets/front/css/custom.css'),
            path.join(projectRoot, 'assets/frontTow/css/demo3.min.css'),
            path.join(projectRoot, 'assets/styles/style.min.css'),

            path.join(projectRoot, 'assets/front/css/all.min.css'),
            path.join(projectRoot, 'assets/front/css/theme-shop.min.css'),
            // path.join(projectRoot, 'assets/frontTow/js/jquery.min.js'),

            // path.join(projectRoot, 'assets/frontTow/js/bootstrap.bundle.min.js'),
            path.join(projectRoot, 'assets/front/js/plugins.min.js'),
            path.join(projectRoot, 'assets/frontTow/js/main.min.js'),
            path.join(projectRoot, 'assets/front/js/theme.js'),
            path.join(projectRoot, 'assets/front/js/theme.init.js'),
            // path.join(projectRoot, 'assets/front/js/custom.js'),

        ],
    },
    output: {
        publicPath: '/',
        path: pathOutput,
        filename: "./js/[name].js",
    },
    resolve: {
        modules: [
            nodeModulesRoot
        ],

        extensions: [
            '*',
            '.js','.jsx',
            '.css', '.scss', '.less',
            '.json', '.yml', 'yaml',
        ]
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    "babel-loader"
                ]
            },
            {
                test: /\.(s*)css$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader
                    },
                    {
                        loader: "css-loader",
                        options: {
                            sourceMap: true,
                            url: false
                        }
                    },
                    {
                        loader: "resolve-url-loader",
                        options: {
                            debug: true,
                            sourceMap: true
                        }
                    },
                ]
            },
            {
                test: /\.(eot|ttf|woff|woff2)$/,
                loader: "file-loader",
                options: {
                    name: "fonts/[name].[ext]"
                }
            }

        ]
    },
    optimization: {
        minimize: false
    },
    performance: {
        hints: false,
        maxEntrypointSize: 512000,
        maxAssetSize: 512000
    },
    plugins: [
        new CopyWebpackPlugin(
            {
                patterns: [
                    {
                        from: path.join(projectRoot, 'assets/backadmin/img'),
                        to: path.join(pathOutput, 'img')
                    },
                    {
                        from: path.join(projectRoot, 'assets/backadmin/plugins'),
                        to: path.join(pathOutput, 'js/jQuery')
                    },
                    {
                        from: path.join(projectRoot, 'assets/front/fonts'),
                        to: path.join(pathOutput, 'fonts')
                    },

                ]
            }),
        new MiniCssExtractPlugin({
            filename: "./css/[name].css",
            chunkFilename: "[id].css"
        }),
        new webpack.ProvidePlugin({
            noUiSlider: 'nouislider'
        }),

        new WebpackAssetsManifest({}),
        new CleanWebpackPlugin()
    ]

};
