(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Request = function(config) {
        config = config || {};

        if (!config.type) {
            config.type = 'json';
        }

        if (!config.method) {
            config.method = 'post';
        }

        if (!config.url) {
            config.url = doc.location;
        }

        if (!config.success) {
            config.success = function() {};
        }

        if (!config.error) {
            config.error = function() {};
        }

        this.xmlreq = new XMLHttpRequest();
        this.config = config;

        this.init();
    }

    win.Solsken.Request.prototype = {
        init: function() {
            var self = this;

            this.xmlreq.onreadystatechange = function() {
                if (self.xmlreq.readyState == XMLHttpRequest.DONE) {
                    var response = self.xmlreq.responseText,
                        success = true;

                    if (self.config.type == 'json') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            success = false;
                        }

                    }

                    if (self.xmlreq.status == 200 && success) {
                        self.config.success(response);
                    } else {
                        self.config.error(response);
                    }
                }
            }
        },

        send: function(data) {
            this.xmlreq.open(this.config.method.toUpperCase(), this.config.url, true);
            this.xmlreq.setRequestHeader("X-Requested-With", "xmlhttprequest");

            switch (this.config.type) {
                case 'json':
                    this.xmlreq.setRequestHeader("Content-Type", "application/json");
                    this.xmlreq.send(JSON.stringify(data));
                    break;

                default:
                    this.xmlreq.send(data);
                    break;
            }

        }
    };
})(window, document);
