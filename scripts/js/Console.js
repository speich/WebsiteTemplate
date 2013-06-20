define([
	'dojo/_base/declare',
	'dojo/_base/lang',
	'dojo/request/notify',
	'dojo/dom-construct',
	'dojo/json',
	'dojo/date/locale',
	'dijit/_WidgetBase'
], function(declare, lang, notify, domConstruct, json, locale, _WidgetBase) {


	/**
	 * @constructor
	 * @alias module:NAFIDAS/batchCalc
	 */
	return declare([_WidgetBase], {

		baseClass: 'nafConsole',

		logOwnMessagesOnly: true,

		constructor: function() {
			notify('done', lang.hitch(this, this.log));
		},

		buildRendering: function() {
			this.inherited('buildRendering', arguments);

			// create the DOM for this widget
			this.domNode.innerHTML = '<label>Log:</label>';

			domConstruct.create('button', {
				'class': 'buttIcon',
				title: 'Log zurücksetzen',
				onclick: lang.hitch(this, function(evt) {
					evt.preventDefault();
					this.clear();
					return false;
				}),
				innerHTML: '<img src="../../layout/images/icon_reset.png">'
			}, this.domNode);

			this.containerNode = domConstruct.create('ul', null, this.domNode);
		},

		/**
		 * Write own message or io error message to log container.
		 * @param {String} response message
		 * @param {Boolean} [isErr] is message an error message
		 */
		log: function(response, isErr) {
			var msg, responseObj, text;

			isErr = isErr || false;

			// request has failed
			if (response instanceof Error) {
				responseObj = response.response;
				isErr = true;
			}
			// request has succeeded
			else {
				responseObj = response;
				// if responseObj is not an object but a string, then log it directly
				if (typeof response === 'string') {
					this.print(response, isErr);
					return;
				}
			}
			// process dojo reponse object
			msg = responseObj.xhr.statusText + ': ' + responseObj.options.method + ' ' + decodeURI(responseObj.url);
			msg += this.extractMsg(responseObj.text);
			this.print(msg, isErr, responseObj.options.method);
		},

		/**
		 * Extract own messages from response body.
		 * @param {String} result json or string
		 * @return {String}
		 */
		extractMsg: function(result) {
			var i, len, msg = '';

			try {
				// response might be json
				result = json.parse(result);
				if (result instanceof Array) {
					len = result.length;
					for (i = 0; i < len; i++) {
						if (result[i].msg) {
							msg += '<br>' + result[i].msg;
						}
					}
				}
			}
			catch (err) {
				// or simple string
				msg += '<br>' + (result.msg || result);
			}
			return msg;
		},

		/**
		 * Print message to HTMLDivElement.
		 * If isErr is set to true, message is printed in red.
		 * If request method is not GET and not an error, message is printed in green.
		 * @param {String} msg message to print
		 * @param {Boolean} isErr is message an error?
		 * @param {String} reqMethod request method
		 */
		print: function(msg, isErr, reqMethod) {
			var date, cl = 'logMsg';

			date = locale.format(new Date(), {
				selector: 'time',
				timePattern: 'HH:mm:ss'
			});
			if (isErr) {
				cl += ' logErrMsg';
			}
			else if (reqMethod !== 'GET') {
				cl += ' logSuccessMsg';			}

			domConstruct.create('li', {
				'class': cl,
				innerHTML: date + ' ' + msg + '<br/>'
			}, this.containerNode, 'first');
		},

		/**
		 * Clear all logged messages.
		 */
		clear: function() {
			this.containerNode.innerHTML = '';
		}

	});
});