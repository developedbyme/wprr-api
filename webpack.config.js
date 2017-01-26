var webpack = require('webpack');
var path = require('path');

module.exports = {

    context: __dirname + "/private/js",
    entry: {
    	"main": "./admin.js",
    },

	output: {
        path: __dirname + "/assets/js",
		filename: "[name].js"
	},

    module: {
        loaders: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
				query: {
					presets: ['es2015', 'react']
				}
            },
            {
                test: /\.json$/,
                exclude: /node_modules/,
                loader: 'json-loader'
            }
        ]
    },
	
	resolve: {
		root: [
			path.resolve('./private/jsapp/')
		],
		extensions: ['', '.js', '.jsx']
	},

    devServer: {
        contentBase: "./public",
        inline: true,
        port: 8080,
        host: 'localhost'
    },

    plugins: [
	// 	new webpack.DefinePlugin({
	// 		'process.env': {
	// 			'NODE_ENV': JSON.stringify('production')
	// 		}
	// 	}),
	// 	new webpack.optimize.UglifyJsPlugin({
	// 		compress: {
	// 			warnings: false,
	// 			drop_console: true,
	// 			drop_debugger: true
	// 		}
	// 	})
	]
}