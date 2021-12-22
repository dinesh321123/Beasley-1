const path = require('path');
const webpack = require('webpack');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');
const CopyPlugin = require('copy-webpack-plugin');

const { ModuleConcatenationPlugin } = webpack.optimize;

function coreConfig(options = {}) {
	const eslintRule = {
		test: /\.js$/,
		enforce: 'pre',
		exclude: /node_modules/,
		use: {
			loader: 'eslint-loader',
			options: {
				failOnWarning: false,
				failOnError: false,
			},
		},
	};

	// TODO: move the babel config to .babelrc
	const babelRule = {
		test: /\.js$/,
		// exclude: /node_modules/,
		include: [
			path.resolve(__dirname, 'assets/scripts'),
			// swiper needs babel transpiling for dom7 and ssr-window
			path.resolve(__dirname, 'node_modules/swiper'),
			path.resolve(__dirname, 'node_modules/dom7'),
			path.resolve(__dirname, 'node_modules/ssr-window'),
		],
		use: {
			loader: 'babel-loader',
			options: {
				cacheDirectory: true,
				presets: [
					'@babel/preset-react',
					[
						'@babel/preset-env',
						{
							useBuiltIns: 'entry',
							modules: false,
							corejs: 3,
						},
					],
				],
				plugins: [
					'@babel/transform-runtime',
					'@babel/plugin-syntax-dynamic-import',
				],
			},
		},
	};

	const cssRule = {
		test: /\.css$/,
		use: [
			{
				loader: MiniCssExtractPlugin.loader,
			},
			{
				loader: 'css-loader',
			},
			{
				loader: 'postcss-loader',
				options: {
					ident: 'postcss',
					plugins(loader) {
						const { postcss } = options;
						const { plugins } = postcss || {};

						const importOptions = {
							root: loader.resourcePath,
						};

						// https://github.com/csstools/postcss-preset-env#usage
						const envOptions = {
							features: {
								'nesting-rules': true,
							},
						};

						return [
							require('postcss-import')(importOptions),
							require('postcss-preset-env')(envOptions),
							require('postcss-custom-media')(),
							...(plugins || []),
						];
					},
				},
			},
		],
	};

	return {
		entry: ['./assets/scripts/index.js'],
		output: {
			path: path.resolve(__dirname, 'bundle'),
			filename: 'app.js',
			chunkFilename: '[name].js',
			publicPath: '/wp-content/themes/experience-engine/bundle/',
		},
		module: {
			rules: [eslintRule, babelRule, cssRule],
		},
		plugins: [
			new MiniCssExtractPlugin(),
			new CopyPlugin({
				patterns: [
					{
						from: './firebase-messaging-sw.js',
						to: '../../../../firebase-messaging-sw.js',
					},
				],
			}),
		],
		optimization: {
			noEmitOnErrors: true,
		},
	};
}

function development() {
	const config = {
		...coreConfig(),
		name: 'dev-config',
		mode: 'development',
		devtool: 'source-map',
	};

	const concatenation = new ModuleConcatenationPlugin();
	config.plugins.push(concatenation);

	return config;
}

function watch() {
	return {
		...coreConfig(),
		name: 'watch-config',
		mode: 'development',
		devtool: 'source-map',
		watch: true,
	};
}

function production() {
	const options = {
		postcss: {
			plugins: [require('cssnano')()],
		},
	};

	const config = {
		...coreConfig(options),
		name: 'prod-config',
		mode: 'production',
	};

	const concatenation = new ModuleConcatenationPlugin();
	config.plugins.push(concatenation);

	return config;
}

function analyze() {
	const config = {
		...coreConfig(),
		name: 'analyze-config',
		mode: 'production',
	};

	const analyzer = new BundleAnalyzerPlugin();
	config.plugins.push(analyzer);

	return config;
}

module.exports = [development(), watch(), production(), analyze()];
