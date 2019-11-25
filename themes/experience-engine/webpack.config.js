const path = require( 'path' );
const webpack = require( 'webpack' );

const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const { BundleAnalyzerPlugin } = require( 'webpack-bundle-analyzer' );
const { ModuleConcatenationPlugin } = webpack.optimize;

function coreConfig( options = {} ) {
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
				presets: ['@babel/preset-react', '@babel/preset-env'],
				plugins: ['@babel/transform-runtime', '@babel/plugin-syntax-dynamic-import'],
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
		plugins: [new MiniCssExtractPlugin()],
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
			plugins: [require( 'cssnano' )()],
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

module.exports = [development(), watch(), production(), analyze()];
