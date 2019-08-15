(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Table = function(table) {
        this.table  = table;
        this.identifier = this.table.getAttribute('data-identifier');
        this.dom    = new win.Solsken.DOM();
        this.cookie = new win.Solsken.Cookie();
        this.config = JSON.parse(this.cookie.get(this.identifier + '_config', '{}'));
        this.initEvents();
    };

    win.Solsken.Table.prototype = {
        update: function() {
            var self = this;

            this.cookie.set(this.identifier + '_config', JSON.stringify(this.config));

            var req = new win.Solsken.Request({
                success: function(res) {
                    if (res.status == 'success') {
                        self.table.innerHTML = res.html;
                        self.initEvents();
                    }
                }
            });

            req.send(this.config);
        },

        initEvents: function() {
            var i, self = this;

            // Click on Row
            if (this.dom.hasClass(this.table, 'table-hover')) {
                var rows = this.dom.getElements('tr', this.table);

                for (i = 0; i < rows.length; i++) {
                    rows[i].addEventListener('click', function(event) {
                        var href = this.getAttribute('data-href');

                        if (href) {
                            doc.location = href;
                        }
                    })
                }
            }

            // Click on pagination
            var pages = this.dom.getElements('a[data-page]');

            for (i = 0; i < pages.length; i++) {
                pages[i].addEventListener('click', function(event) {
                    event.preventDefault();

                    self.config[self.identifier + '_page'] = this.getAttribute('data-page');

                    self.update();
                });
            }

            // Click on Order
            var orders = this.dom.getElements('a[data-order]');

            for (i = 0; i < orders.length; i++) {
                orders[i].addEventListener('click', function(event) {
                    event.preventDefault();

                    var key = this.getAttribute('data-order');

                    if (self.config[self.identifier + '_order'] && self.config[self.identifier + '_order'][key]) {
                        switch (self.config[self.identifier + '_order'][key]) {
                            case 'ASC':
                                self.config[self.identifier + '_order'][key] = 'DESC';
                                break;

                            case 'DESC':
                                self.config[self.identifier + '_order'][key] = 'ASC';
                                break;
                        }
                    } else {
                        self.config[self.identifier + '_order'] = {};
                        self.config[self.identifier + '_order'][key] = 'ASC';
                    }

                    self.update();
                });
            }
        }
    };
})(window, document);
