(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Table = function(wrapper) {
        this.dom        = new win.Solsken.DOM();
        this.wrapper    = wrapper;
        this.table      = this.dom.getElement('table', wrapper);
        this.identifier = this.wrapper.getAttribute('data-identifier');
        this.cookie     = new win.Solsken.Cookie();
        this.config     = JSON.parse(this.cookie.get(this.identifier + '_config', '{}'));

        this.config.table_identifier = this.identifier;

        this.initEvents();
    };

    win.Solsken.Table.prototype = {
        update: function() {
            var self = this;

            this.cookie.set(this.identifier + '_config', JSON.stringify(this.config));

            var req = new win.Solsken.Request({
                success: function(res) {
                    if (res.status == 'success') {
                        self.wrapper.innerHTML = res.html;
                        self.table             = self.dom.getElement('table', self.wrapper);

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
            var pages = this.dom.getElements('a[data-page]', this.wrapper);

            for (i = 0; i < pages.length; i++) {
                pages[i].addEventListener('click', function(event) {
                    event.preventDefault();

                    self.config[self.identifier + '_page'] = this.getAttribute('data-page');

                    self.update();
                });
            }

            // Click on Order
            var orders = this.dom.getElements('a[data-order]', this.wrapper);

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

            // Filters
            var filters = this.dom.getElements('.column-filter', this.wrapper);

            for (i = 0; i < filters.length; i++) {

                switch (filters[i].tagName.toLowerCase()) {
                    case 'input':
                        filters[i].addEventListener('keyup', function(event) {
                            if (event.keyCode == 13) {
                                var key = this.getAttribute('data-key');

                                self.config[self.identifier + '_filter'] = self.config[self.identifier + '_filter'] || {};
                                self.config[self.identifier + '_filter'][key] = this.value;
                                self.update();
                            }
                        });
                        break;
                }
            }
        }
    };
})(window, document);
