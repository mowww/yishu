var Base_collections = Backbone.Collection.extend( {
	_total_rows : 0,
	_url : '',
	total_rows : function() {
		return this._total_rows > 0 ? this._total_rows : 0;
	},
	url : function() {
		return this._url + this.query_strings();
	},
	parse : function(response) {
		if (response.code == 1) {
			this._total_rows = parseInt(response.data.total_rows, 10);
			return response.data.list;
		}
		return null;
	},
	fetch_reset : function() {
		this.fetch( {
			reset : true
		});
	},
	settings : {},
	get_setting : function(key) {
		if (this.settings[key] != undefined) {
			return this.settings[key];
		}
		return false;
	},
	query_strings : function() {
		var ret = [];
		for ( var k in this.defaults) {
			var v = this.get_setting(k);
			if (k != 'cur_page') {
				ret.push(k + "=" + v);
			}
		}
		return this.get_setting('cur_page') + '/?' + ret.join("&");
	},
	set_data : function(config) {
		if (_.size(this.settings) == 0) {
			this.settings = Backbone.$.extend( {}, this.defaults, config);
		} else {
			Backbone.$.extend(this.settings, config);
		}
	},
	defaults : {
		sort_key : '',
		sort_value : '',
		cur_page : 0,
		per_page : 10,
		keyword : ''
	}
});
var Base_view = Backbone.View.extend( {
	childs : function(val, type) {
		var hasType = type != undefined && typeof (type) == "string";
		var cid = val.cid;
		hasType ? cid = type + "#" + cid : '';
		this[cid] = val;
		this.child_index = cid;
	},
	child : function(val) {
		return this[this.child_index];
	},
	removes : function(type) {
		var hasType = type != undefined && typeof (type) == "string";
		var patt = new RegExp("^" + type + "#");
		for ( var prop in this) {
			if (hasType && !patt.test(prop)) {
				continue;
			}
			if (this[prop] instanceof Backbone.View) {
				this[prop].removes();
				delete this[prop];
			}
		}
		if (!hasType) {
			this.remove();
		}
	},
	debug : function(str, val) {
		if (this.development()) {
			if (window.console)
				console.log(str, (val != undefined ? ' ' + JSON.stringify(val) : ''));
		}
	},
	uris : function() {
		var hash = window.location.href.replace(/^[#\/\!]+/, '');
		var search = hash.split('?');
		return search.shift().split('/');
	},
	development : function() {
		var acts = this.uris();
		return /^test/.test(acts['2']);
	}
});
var Intent_model = Backbone.Model.extend( {});