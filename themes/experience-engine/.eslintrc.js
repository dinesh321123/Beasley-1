module.exports = {
	settings: {
		react: {
			version: '16.5.2',
		},
	},
	parser: 'babel-eslint',
	env: {
		browser: true,
		node: true,
		es6: true,
	},
	plugins: ['import', 'react', 'jsx-a11y'],
	extends: [
		'@10up/eslint-config',
		'plugin:import/errors',
		'plugin:import/warnings',
		'plugin:react/recommended',
		'plugin:jsx-a11y/recommended',
	],
	globals: {
		wp: true,
	},
	env: {
		browser: true,
		node: true,
	},
	rules: {
		'comma-dangle': [
			'warn',
			{
				arrays: 'always-multiline',
				objects: 'always-multiline',
				imports: 'always-multiline',
				exports: 'always-multiline',
				functions: 'always-multiline',
			},
		],
		'import/no-unresolved': 0,
		'no-extra-semi': 0,
		'no-unreachable': 0,
		'no-unused-vars': 0,
		'no-useless-escape': 0,
		// @note: override @10up/eslint-config
		'require-jsdoc': 0,
		'camelcase': 0,

		// @note: Turning this off, confirming this is WordPress standard
		// 'sort-keys': [1, 'asc', { caseSensitive: true, natural: false }],
		indent: [
			'error',
			'tab',
			{
				SwitchCase: 1,
			},
		],
		'jsx-a11y/label-has-for': [
			'error',
			{
				required: {
					every: ['id'],
				},
			},
		],
		'jsx-a11y/media-has-caption': 0,
		'jsx-a11y/anchor-is-valid': 0
	},
};
