module.exports = {
	parser: 'babel-eslint',
	parserOptions: {
		ecmaFeatures: {
			generators: true,
			experimentalObjectRestSpread: true
		},
		sourceType: 'module',
		allowImportExportEverywhere: false
	},
	env: {
		browser: true,
		es6: true
	},
	plugins: ['prettier', 'react-hooks'],
	extends: ['airbnb', 'prettier', 'prettier/react'],
	settings: {
		'import/resolver': {
			node: {
				extensions: ['.js', '.json', '.css', '.styl']
			}
		}
	},
	globals: {
		window: true,
		document: true,
		__dirname: true,
		__DEV__: true,
		CONFIG: true,
		process: true,
		jest: true,
		describe: true,
		test: true,
		it: true,
		expect: true,
		beforeEach: true,
		fetch: true,
		alert: true,
		module: true,
		require: true,
		history: true,
		location: true,
	},
	rules: {
		'no-param-reassign': 0,
		'no-plusplus': 0,
		'react/destructuring-assignment': 0,
		'camelcase': 0,
		'jsx-a11y/media-has-caption': 0,
		'jsx-a11y/anchor-is-valid': 0,
		'no-restricted-globals': 0,
		'no-shadow': 0,
		'no-tabs': 0,
		'no-continue': 0,
		'no-restricted-globals': 0,
		'no-use-before-define': 0,
		'jsx-a11y/label-has-associated-control': 0,
		'react/sort-comp': 0,
		'consistent-return': 0,
		'no-underscore-dangle': 0,
		'class-methods-use-this': 0,
		'max-len': 0,
		'global-require': 0,
		'import/no-dynamic-require': 0,
		'indent': [2, 'tab', { 'SwitchCase': 1 }],
		'react/jsx-indent': [2, 'tab'],
		'react/jsx-filename-extension': 0,
		'react/no-danger': 0,
		'no-unused-vars': [ 'error', { args: 'none' } ],
		// See https://github.com/evcohen/eslint-plugin-jsx-a11y/issues/340
		"jsx-a11y/anchor-is-valid": [ "error", {
				"components": [ "Link" ],
				"specialLink": [ "to" ]
		}],
		"jsx-a11y/label-has-for": [ 2, {
			"components": [ "Label" ],
			"required": {
				"some": [ "nesting", "id" ]
			},
		}],
		'prettier/prettier': [ 'error', { useTabs: true, tabWidth: 2, singleQuote: true, printWidth: 80, trailingComma: "all" } ],
		"react-hooks/rules-of-hooks": "error", // Checks rules of Hooks
	},
};
