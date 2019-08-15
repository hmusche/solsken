(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Cookie = function() {
        this.prefix = 'SLSKN';
    }

    win.Solsken.Cookie.prototype = {
        acceptCookie: function(elem) {
            var dom = new Solsken.DOM();
            this.set('accept', 1);
            dom.getParent('.cookie-notice', elem).style.display = 'none';
        },

        getKey: function(key) {
            return this.prefix + '---' + key;
        },

        /**
         * Set a Cookie
         * @param  {string} key    key to set, prepended by prefix
         * @param  {string} value  value to set
         * @param  {string} path   path to set
         * @param  {string} maxAge maxAge in days
         */
        set: function(key, value, path, maxAge) {
            if (typeof value == 'boolean') {
                value = value ? 1 : 0;
            }

            var cookieParts = [
                this.getKey(key) + "=" + value
            ];

            path = path || "/";
            maxAge = maxAge || 365;
            maxAge = maxAge * 86400;

            cookieParts.push('path=' + path);
            cookieParts.push('max-age=' + maxAge);

            document.cookie = cookieParts.join(';');
        },

        get: function(key, def) {
            var check = new RegExp(this.getKey(key) + '=([^;]+)'),
                match = document.cookie.match(check);

            if (match && match[1]) {
                return match[1];
            } else {
                return def;
            }
        }
    };
})(window, document);
