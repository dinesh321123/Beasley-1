const path = require( 'path' );
const webpack = require( 'webpack' );

const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const { BundleAnalyzerPlugin } = require( 'webpack-bundle-analyzer' );

const coreConfig = ( options = {} ) => ( {
	output: {
		path: path.resolve( __dirname, 'bundle' ),
		filename: '[name].js',
		chunkFilename: '[name].js',
		publicPath: '/wp-content/themes/experience-engine/bundle/',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				enforce: 'pre',
				exclude: /node_modules/,
				use: {
					loader: 'eslint-loader',
					options: {
						failOnWarning: false,
						failOnError: true,
					},
				},
			},
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						cacheDirectory: true,
						presets: ['@babel/preset-react', '@babel/preset-env'],
						plugins: ['@babel/plugin-syntax-dynamic-import'],
					},
				},
			},
			{
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
							plugins( loader ) {
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
									require( 'postcss-import' )( importOptions ),
									require( 'postcss-custom-media' )(),
									require( 'postcss-preset-env' )( envOptions ),
									...( plugins || [] ),
								];
							},
						},
					},
				],
			},
		],
	},
	plugins: [
		new MiniCssExtractPlugin(),

		new CopyWebpackPlugin( [
			// video.js
			'node_modules/video.js/dist/video-js.min.css',
			'node_modules/video.js/dist/video.min.js',

			// videojs-contrib-quality-levels & videojs-hls-quality-selector
			'node_modules/videojs-contrib-quality-levels/dist/videojs-contrib-quality-levels.min.js',
			'node_modules/videojs-hls-quality-selector/dist/videojs-hls-quality-selector.min.js',

			// videojs-contrib-ads & videojs-ima
			'node_modules/videojs-contrib-ads/dist/videojs-contrib-ads.css',
			'node_modules/videojs-contrib-ads/dist/videojs-contrib-ads.min.js',
			'node_modules/videojs-ima/dist/videojs.ima.css',
			'node_modules/videojs-ima/dist/videojs.ima.min.js',
		] ),
	],
	optimization: {
		noEmitOnErrors: true,
	},
} );

const development = () => {
	const config = Object.assign( coreConfig(), {
		name: 'dev-config',
		mode: 'development',
		devtool: 'eval',
	} );

	return config;
};

const watch = () => {
	const config = Object.assign( coreConfig(), {
		name: 'watch-config',
		mode: 'development',
		devtool: 'eval',
		watch: true,
	} );

	return config;
};

const production = () => {
	const options = {
		postcss: {
			plugins: [
				require( 'cssnano' )(),
			],
		},
	};

	const config = Object.assign( coreConfig( options ), {
		name: 'prod-config',
		mode: 'production',
	} );

	return config;
};

const analyze = () => {
	const config = Object.assign( coreConfig(), {
		name: 'analyze-config',
		mode: 'production',
	} );

	const analyzer = new BundleAnalyzerPlugin();
	config.plugins.push( analyzer );

	return config;
};

module.exports = [development(), watch(), production(), analyze()];
