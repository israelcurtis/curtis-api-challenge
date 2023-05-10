const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');

module.exports = {
	...defaultConfig,
	output: {
		path: path.join(__dirname, '/includes/blocks'),
		filename: '[name].js'
	}
}
