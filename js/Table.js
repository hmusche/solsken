(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Table = function(table) {
        this.table = table;
        this.dom = new win.Solsken.DOM();
        this.initEvents();
    };

    win.Solsken.Table.prototype = {
        initEvents: function() {
            if (this.dom.hasClass(this.table, 'table-hover')) {
                var i, rows = this.dom.getElements('tr', this.table);

                for (i = 0; i < rows.length; i++) {
                    rows[i].addEventListener('click', function(event) {
                        var href = this.getAttribute('data-href');

                        if (href) {
                            doc.location = href;
                        }
                    })
                }
            }
        }
    };
})(window, document);
