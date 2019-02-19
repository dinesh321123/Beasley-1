const path = require( 'path' );
const webpack = require( 'webpack' );

const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const { BundleAnalyzerPlugin } = require( 'webpack-bundle-analyzer' );
const { ModuleConcatenationPlugin } = webpack.optimize;

function coreConfig( options = {} ) {
	const copyPluginArgs = [
		// core-js
		'node_modules/core-js/client/core.min.js',

		// video.js & videojs-flash
		'node_modules/video.js/dist/video-js.min.css',
		'node_modules/video.js/dist/video.min.js',
		'node_modules/videojs-flash/dist/videojs-flash.min.js',

		// videojs-contrib-hls & videojs-contrib-quality-levels & videojs-hls-quality-selector
		'node_modules/videojs-contrib-hls/dist/videojs-contrib-hls.min.js',
		'node_modules/videojs-contrib-quality-levels/dist/videojs-contrib-quality-levels.min.js',
		'node_modules/videojs-hls-quality-selector/dist/videojs-hls-quality-selector.min.js',

		// videojs-contrib-ads & videojs-ima
		'node_modules/videojs-contrib-ads/dist/videojs-contrib-ads.css',
		'node_modules/videojs-contrib-ads/dist/videojs-contrib-ads.min.js',
		'node_modules/videojs-ima/dist/videojs.ima.css',
		'node_modules/videojs-ima/dist/videojs.ima.min.js',
	];

	const eslintRule = {
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
	};

	const babelRule = {
		test: /\.js$/,
		exclude: /node_modules/,
		use: {
			loader: 'babel-loader',
			options: {
				cacheDirectory: true,
				presets: [
					'@babel/preset-react',
					'@babel/preset-env',
				],
				plugins: [
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
							require( 'postcss-preset-env' )( envOptions ),
							require( 'postcss-custom-media' )(),
							...( plugins || [] ),
						];
					},
				},
			},
		],
	};

	return {
		output: {
			path: path.resolve( __dirname, 'bundle' ),
			filename: '[name].js',
			chunkFilename: '[name].js',
			publicPath: '/wp-content/themes/experience-engine/bundle/',
		},
		externals: {
			firebase: 'firebase',
			react: 'React',
			'react-dom': 'ReactDOM',
		},
		module: {
			rules: [eslintRule, babelRule, cssRule],
		},
		plugins: [
			new MiniCssExtractPlugin(),
			new CopyWebpackPlugin( copyPluginArgs ),
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
		devtool: 'inline-source-map',
	};

	const concatenation = new ModuleConcatenationPlugin();
	config.plugins.push( concatenation );

	return config;
}

function watch() {
	return {
		...coreConfig(),
		name: 'watch-config',
		mode: 'development',
		devtool: 'inline-source-map',
		watch: true,
	};
}

function production() {
	const options = {
		postcss: {
			plugins: [
				require( 'cssnano' )(),
			],
		},
	};

	const config = {
		...coreConfig( options ),
		name: 'prod-config',
		mode: 'production',
	};

	const concatenation = new ModuleConcatenationPlugin();
	config.plugins.push( concatenation );

	return config;
}

function analyze() {
	const config = {
		...coreConfig(),
		name: 'analyze-config',
		mode: 'production',
	};

	const analyzer = new BundleAnalyzerPlugin();
	config.plugins.push( analyzer );

	return config;
}

module.exports = [
	development(),
	watch(),
	production(),
	analyze(),
];
