const path = require('path');
const fs = require('fs');

const webpack = require('webpack');

const nodeEnv = process.env.NODE_ENV || 'development';
const isProduction = 'production' === nodeEnv;

// webpack config
const config = {};

// entry point
config.entry = {
	media: './assets/src/index.js',
};

// devtools
config.devtool = isProduction ? false : 'source-map';

// output
config.output = {
	path: path.resolve(__dirname, 'assets/dist'),
	publicPath: '/',
	filename: '[name].js',
	chunkFilename: '[id].js',
};

// define module object
config.module = {
	rules: []
};

// eslint configuration
config.module.rules.push({
	test: /\.js$/,
	enforce: 'pre',
	exclude: /node_modules/,
	use: [
		{
			loader: 'eslint-loader',
			options: {
				failOnWarning: false,
				failOnError: true
			}
		}
	]
});

// babel loader rule
config.module.rules.push({
	test: /\.js$/,
	exclude: /node_modules/,
	use: {
		loader: 'babel-loader',
		options: {
			cacheDirectory: true,
			presets: [
				['env', { targets: { browsers: ["last 2 versions", "safari >= 7"] } }]
			]
		}
	}
});

// style loader rule
config.module.rules.push({
	test: /\.css$/,
	use: [
		{
			loader: "style-loader"
		},
		{
			loader: "css-loader"
		}
	]
});

// define plugins array
config.plugins = [
	new webpack.NoEmitOnErrorsPlugin(),

	new webpack.DefinePlugin({
		'process.env': {
			NODE_ENV: JSON.stringify(nodeEnv)
		}
	})
];

// uglify plugin
if (isProduction) {
	config.plugins.push(
		new webpack.optimize.UglifyJsPlugin({
			compress: {warnings: false},
			output: {comments: false},
			sourceMap: true
		})
	);
}

module.exports = config;
