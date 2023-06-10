'use strict';

window.chartColors = {
	aliceblue: '#F0F8FF',
	antiquewhite: '#FAEBD7',
	aqua: '#00FFFF',
	aquamarine: '#7FFFD4',
	azure: '#F0FFFF',
	beige: '#F5F5DC',
	bisque: '#FFE4C4',
	black: '#000000',
	blanchedalmond: '#FFEBCD',
	blue: '#0000FF',
	blueviolet: '#8A2BE2',
	brown: '#A52A2A',
	burlywood: '#DEB887',
	cadetblue: '#5F9EA0',
	chartreuse: '#7FFF00',
	chocolate: '#D2691E',
	coral: '#FF7F50',
	cornflowerblue: '#6495ED',
	cornsilk: '#FFF8DC',
	crimson: '#DC143C',
	cyan: '#00FFFF',
	darkblue: '#00008B',
	darkcyan: '#008B8B',
	darkgray: '#A9A9A9',
	darkgreen: '#006400',
	darkkhaki: '#BDB76B',
	darkmagenta: '#8B008B',
	darkolivegreen: '#556B2F',
	darkorange: '#FF8C00',
	darkorchid: '#9932CC',
	darkred: '#8B0000',
	darksalmon: '#E9967A',
	darkseagreen: '#8FBC8F',
	darkslateblue: '#483D8B',
	darkslategray: '#2F4F4F',
	darkturquoise: '#00CED1',
	darkviolet: '#9400D3',
	deeppink: '#FF1493',
	deepskyblue: '#00BFFF',
	dimgray: '#696969',
	dodgerblue: '#1E90FF',
	firebrick: '#B22222',
	floralwhite: '#FFFAF0',
	forestgreen: '#228B22',
	fuchsia: '#FF00FF',
	gainsboro: '#DCDCDC',
	ghostwhite: '#F8F8FF',
	gold: '#FFD700',
	goldenrod: '#DAA520',
	gray: '#808080',
	green: '#008000',
	greenyellow: '#ADFF2F',
	honeydew: '#F0FFF0',
	hotpink: '#FF69B4',
	indianred: '#CD5C5C',
	indigo: '#4B0082',
	ivory: '#FFFFF0',
	khaki: '#F0E68C',
	lavender: '#E6E6FA',
	lavenderblush: '#FFF0F5',
	lawngreen: '#7CFC00',
	lemonchiffon: '#FFFACD',
	lightblue: '#ADD8E6',
	lightcoral: '#F08080',
	lightcyan: '#E0FFFF',
	lightgoldenrodyellow: '#FAFAD2',
	lightgray: '#D3D3D3',
	lightgreen: '#90EE90',
	lightpink: '#FFB6C1',
	lightsalmon: '#FFA07A',
	lightseagreen: '#20B2AA',
	lightskyblue: '#87CEFA',
	lightslategray: '#778899',
	lightsteelblue: '#B0C4DE',
	lightyellow: '#FFFFE0',
	lime: '#00FF00',
	limegreen: '#32CD32',
	linen: '#FAF0E6',
	magenta: '#FF00FF',
	maroon: '#800000',
	mediumaquamarine: '#66CDAA',
	mediumblue: '#0000CD',
	mediumorchid: '#BA55D3',
	mediumpurple: '#9370DB',
	mediumseagreen: '#3CB371',
	mediumslateblue: '#7B68EE',
	mediumspringgreen: '#00FA9A',
	mediumturquoise: '#48D1CC',
	mediumvioletred: '#C71585',
	midnightblue: '#191970',
	mintcream: '#F5FFFA',
	mistyrose: '#FFE4E1',
	moccasin: '#FFE4B5',
	navajowhite: '#FFDEAD',
	navy: '#000080',
	oldlace: '#FDF5E6',
	olive: '#808000',
	olivedrab: '#6B8E23',
	orange: '#FFA500',
	orangered: '#FF4500',
	orchid: '#DA70D6',
	palegoldenrod: '#EEE8AA',
	palegreen: '#98FB98',
	paleturquoise: '#AFEEEE',
	palevioletred: '#DB7093',
	papayawhip: '#FFEFD5',
	peachpuff: '#FFDAB9',
	peru: '#CD853F',
	pink: '#FFC0CB',
	plum: '#DDA0DD',
	powderblue: '#B0E0E6',
	purple: '#800080',
	red: '#FF0000',
	rosybrown: '#BC8F8F',
	royalblue: '#4169E1',
	saddlebrown: '#8B4513',
	salmon: '#FA8072',
	sandybrown: '#F4A460',
	seagreen: '#2E8B57',
	seashell: '#FFF5EE',
	sienna: '#A0522D',
	silver: '#C0C0C0',
	skyblue: '#87CEEB',
	slateblue: '#6A5ACD',
	slategray: '#708090',
	snow: '#FFFAFA',
	springgreen: '#00FF7F',
	steelblue: '#4682B4',
	tan: '#D2B48C',
	teal: '#008080',
	thistle: '#D8BFD8',
	tomato: '#FF6347',
	turquoise: '#40E0D0',
	violet: '#EE82EE',
	wheat: '#F5DEB3',
	white: '#FFFFFF',
	whitesmoke: '#F5F5F5',
	yellow: '#FFFF00',
	yellowgreen: '#9ACD32'
};

(function(global) {
	var MONTHS = [
		'Enero',
		'Febrero',
		'Marzo',
		'Abril',
		'Mayo',
		'Junio',
		'Julio',
		'Agosto',
		'Septiembre',
		'Octubre',
		'Noviembre',
		'Diciembre'
	];

	var COLORS = [
		'#4dc9f6',
		'#f67019',
		'#f53794',
		'#537bc4',
		'#acc236',
		'#166a8f',
		'#00a950',
		'#58595b',
		'#8549ba'
	];

	var Samples = global.Samples || (global.Samples = {});
	var Color = global.Color;

	Samples.utils = {
		// Adapted from http://indiegamr.com/generate-repeatable-random-numbers-in-js/
		srand: function(seed) {
			this._seed = seed;
		},

		rand: function(min, max) {
			var seed = this._seed;
			min = min === undefined ? 0 : min;
			max = max === undefined ? 1 : max;
			this._seed = (seed * 9301 + 49297) % 233280;
			return min + (this._seed / 233280) * (max - min);
		},

		numbers: function(config) {
			var cfg = config || {};
			var min = cfg.min || 0;
			var max = cfg.max || 1;
			var from = cfg.from || [];
			var count = cfg.count || 8;
			var decimals = cfg.decimals || 8;
			var continuity = cfg.continuity || 1;
			var dfactor = Math.pow(10, decimals) || 0;
			var data = [];
			var i, value;

			for (i = 0; i < count; ++i) {
				value = (from[i] || 0) + this.rand(min, max);
				if (this.rand() <= continuity) {
					data.push(Math.round(dfactor * value) / dfactor);
				} else {
					data.push(null);
				}
			}

			return data;
		},

		labels: function(config) {
			var cfg = config || {};
			var min = cfg.min || 0;
			var max = cfg.max || 100;
			var count = cfg.count || 8;
			var step = (max - min) / count;
			var decimals = cfg.decimals || 8;
			var dfactor = Math.pow(10, decimals) || 0;
			var prefix = cfg.prefix || '';
			var values = [];
			var i;

			for (i = min; i < max; i += step) {
				values.push(prefix + Math.round(dfactor * i) / dfactor);
			}

			return values;
		},

		months: function(config) {
			var cfg = config || {};
			var count = cfg.count || 12;
			var section = cfg.section;
			var values = [];
			var i, value;

			for (i = 0; i < count; ++i) {
				value = MONTHS[Math.ceil(i) % 12];
				values.push(value.substring(0, section));
			}

			return values;
		},

		color: function(index) {
			return COLORS[index % COLORS.length];
		},

		transparentize: function(color, opacity) {
			var alpha = opacity === undefined ? 0.5 : 1 - opacity;
			return Color(color).alpha(alpha).rgbString();
		}
	};

	// DEPRECATED
	window.randomScalingFactor = function() {
		return Math.round(Samples.utils.rand(-100, 100));
	};

	// INITIALIZATION

	Samples.utils.srand(Date.now());

	// Google Analytics
	/* eslint-disable */
	if (document.location.hostname.match(/^(www\.)?chartjs\.org$/)) {
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-28909194-3', 'auto');
		ga('send', 'pageview');
	}
	/* eslint-enable */

}(this));
