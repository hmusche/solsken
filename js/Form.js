(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Form = function(form) {
        this.form = form;

        this.setDateFields();
    };

    win.Solsken.Form.prototype = {
        setDateFields: function() {
            var i,
                dom    = new Solsken.DOM(),
                inputs = dom.getElements('input[data-type=date]', this.form),
                locale = new Solsken.Locale();

            for (i = 0; i < inputs.length; i++) {
                var input = inputs[i];

                var updateTs = function() {
                        var date = new Date(dateInput.value + " " + timeInput.value);

                        input.value = Math.round(date.getTime() / 1000);
                    },
                    ts = input.value,
                    dateInput = dom.createElement('input', {
                        'class': input.className,
                        'type': 'date',
                        'events': {
                            change: updateTs
                        }
                    }),
                    timeInput = dom.createElement('input', {
                        'class': input.className,
                        'type': 'time',
                        'events': {
                            change: updateTs
                        }
                    });

                if (ts) {
                    dateInput.value = locale.formatTs('yyyy-MM-dd', ts);
                    timeInput.value = locale.formatTs('hh:mm', ts);
                }

                input.style.display = 'none';
                dom.inject(dateInput, dom.getParent('.form-group', input));
                dom.inject(timeInput, dom.getParent('.form-group', input));
            };
        }
    };
})(window, document);
