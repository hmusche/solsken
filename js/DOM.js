(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.DOM = function() {};

    win.Solsken.DOM.prototype = {
        getElement: function(identifier, top) {
            top = top || doc;

            return top.querySelector(identifier);
        },

        getElements: function(identifier, top) {
            top = top || doc;

            return top.querySelectorAll(identifier);
        },

        testIdentifier: function(identifier, element) {
            if (!element.parentNode) {
                return null;
            }

            var i,
                parent = element.parentNode,
                siblings = parent.querySelectorAll(identifier);

            for (i = 0; i < siblings.length; i++) {
                if (siblings[i] === element) {
                    return true;
                }
            }

            return false;
        },

        getParent: function(identifier, element) {
            while (element && element.parentNode) {
                element = element.parentNode;

                if (this.testIdentifier(identifier, element)) {
                    return element;
                }
            }

            return null;
        },

        getPrevious: function(identifier, element) {
            var previousSibling = element.previousSibling;

            while(previousSibling) {
                if (this.testIdentifier(identifier, previousSibling)) {
                    return previousSibling;
                } else {
                    previousSibling = previousSibling.previousSibling
                }
            }
        },

        getNext: function(identifier, element) {
            var nextSibling = element.nextSibling;

            while(nextSibling) {
                if (this.testIdentifier(identifier, nextSibling)) {
                    return nextSibling;
                } else {
                    nextSibling = nextSibling.nextSibling
                }
            }
        },

        createElement: function(tag, options) {
            options = options || {};

            var i, j,
                element = doc.createElement(tag),
                optKeys = Object.keys(options);

            for (i = 0; i < optKeys.length; i++) {
                switch (optKeys[i]) {
                    case 'events':
                        var eventKeys = Object.keys(options.events);

                        for (j = 0; j < eventKeys.length; j++) {
                            element.addEventListener(eventKeys[j], options.events[eventKeys[j]]);
                        }
                        break;

                    case 'text':
                        element.innerHTML = options[optKeys[i]];
                        break;

                    default:
                        element.setAttribute([optKeys[i]], options[optKeys[i]]);
                        break;
                }
            }

            return element;
        },

        inject: function(element, parent) {
            parent.appendChild(element);
        },

        hasClass: function(element, className) {
            const regex = new RegExp('(?:^|\\s)' + className + '(?!\\S)');

            return element.className.search(regex) > -1;
        },

        addClass: function(element, className) {
            if (!this.hasClass(element, className)) {
                element.className = element.className + ' ' + className;
            }
        },

        removeClass: function(element, className) {
            const regex = new RegExp('(?:^|\\s)' + className + '(?!\\S)');

            if (this.hasClass(element, className)) {
                element.className = element.className.replace(regex, '');
            }
        },

        toggleClass: function(element, className) {
            if (this.hasClass(element, className)) {
                this.removeClass(element, className);
            } else {
                this.addClass(element, className);
            }
        }
    };
})(window, document);
