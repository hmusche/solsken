(function(win, doc) {
    win.Solsken = win.Solsken || {};

    win.Solsken.Gallery = function(gallery) {
        this.dom = new Solsken.DOM();
        this.gallery = gallery;
        this.imageContainer = this.dom.getElement('.image-wrapper', gallery);
        this.galleryWrapper = this.dom.getElement('.gallery-wrapper', gallery);
        this.controls = this.dom.getElement('.gallery-controls', gallery);
        this.playvideo = this.dom.getElement('.gallery-playvideo-button');
        this.offset = 0;
        this.currentIndex = 0;
        this.maxRatio     = 3;
        this.getFullscreenPrefixes();
        this.setImages();
        this.cookie = new Solsken.Cookie();
    }

    win.Solsken.Gallery.prototype = {
        setImages: function(currentSize) {
            var i,
                self = this,
                sizes = {
                    'xl': 1920,
                    'lg': 1200,
                    'md': 900,
                    'sm': 600,
                    'xs': 300,
                };

            if (!currentSize) {
                for (i = 0; i < Object.keys(sizes).length; i++) {

                    if (self.gallery.clientWidth < Object.values(sizes)[i]) {
                        currentSize = Object.keys(sizes)[i];
                    }
                }
            }

            this.images = this.dom.getElements('.gallery-image-wrapper', this.galleryWrapper);
            this.loadedImages = 0;

            this.setSizes(true);

            for (i = 0; i < this.images.length; i++) {
                (function(div) {
                    var imgSec = div.getAttribute('data-src').replace('{size}', currentSize),
                        subtitle = div.getAttribute('data-subtitle'),
                        filetype = div.getAttribute('data-filetype');

                    if (!self.dom.hasClass(self.controls, 'done')) {

                        self.dom.inject(self.dom.createElement('div', {
                            'class': 'gallery-bullet',
                            'data-index': i
                        }), self.dom.getElement('.gallery-bullets', self.controls));
                    }

                    if (filetype.indexOf('image') === 0) {
                        var loader = self.dom.getElement('.loader', div);
                        loader && self.dom.removeClass(loader, 'd-none');
                        self.dom.createElement('img', {
                            'src': imgSec,
                            'events': {
                                'load': function() {
                                    self.loadedImages++;

                                    div.style['background-image'] = 'url(' + imgSec + ')';

                                    loader && loader.remove()
                                    this.remove();

                                    if (subtitle) {
                                        var sub = self.dom.createElement('div', {
                                            'class': 'subtitle',
                                            'text' : subtitle
                                        });

                                        self.dom.inject(sub, div);
                                    }

                                    if ((this.height / this.width) < self.maxRatio) {
                                        self.maxRatio = (this.height / this.width);
                                    }

                                    self.setSizes();
                                }
                            }
                        });
                    } else if (filetype.indexOf('video') === 0 && !div.hasClass('video-done')) {
                        var vid = self.dom.createElement('video', {
                            'src': imgSrc,
                            'controls': false
                        });
        
                        var progressWrapper = self.dom.createElement('div', {
                            'class': 'progress-wrapper'
                        });
        
                        var progress = self.dom.createElement('div', {
                            'class': 'progress'
                        });
        
                        self.dom.inject(progress, progressWrapper);
        
                        if (vid.addEventListener) {
                            vid.addEventListener('canplay', function() {
                                div.getElement('.loader') && div.getElement('.loader').addClass('d-none');
        
                                if (!vid.retrieve('playing')) {
                                    self.playvideo.removeClass('d-none');
                                }
                            });
        
                            vid.addEventListener('waiting', function() {
                                div.getElement('.loader') && div.getElement('.loader').removeClass('d-none');
        
                            });
        
                            vid.addEventListener('playing', function() {
                                div.getElement('.loader') && div.getElement('.loader').addClass('d-none');
                            });
        
                            vid.addEventListener('ended', function() {
                                self.toggleVideo(true);
                            });
        
                            vid.addEventListener('timeupdate', function(event) {
                                if (this.duration && this.currentTime) {
                                    var ratio = this.currentTime / this.duration * 100,
                                        progress = this.getPrevious().getElement('.progress');
        
                                    progress.setStyle('width', ratio + '%');
                                }
        
                            });
                        }
        
                        self.loadedImages++;
                        progressWrapper.inject(div);
                        vid.inject(div);
        
                        if (subtitle) {
                            new Element('div', {
                                'class': 'subtitle',
                                'text' : subtitle
                            }).inject(div);
                        }
        
                        div.addClass('video-done');
                    }
                })(this.images[i]);
            }

            this.initEvents();
            this.toggleEasing(true);
        },

        setSizes: function(initial) {
            var i,
                self = this,
                width = this.gallery.clientWidth,
                height = width * this.maxRatio;

            if (initial) {
                // On initial run, set ratio to 2:1
                height = width * 0.5;
            }

            if (this.dom.hasClass(this.gallery, 'fullscreen')) {
                height = this.gallery.clientHeight;
            }

            this.gallery.style.height = height + 'px';
            this.toggleEasing(false);

            /**
             * Set all images width to maximum of gallery wrapper
             */
            for (i = 0; i < this.images.length; i++) {
                this.images[i].style.height = height + 'px';
                this.images[i].style.width = width + 'px';
            };

            self.galleryWrapper.style.width = width * (this.images.length + 1) + 'px';

            this.showImage();
            this.toggleEasing(true);
        },

        initEvents: function() {
            var self = this,
                current, initial, max, min;

            if (!this.dom.hasClass(this.controls, 'done')) {
                this.gallery.addEventListener('touchstart', function(event) {
                    var elem = this;

                    self.toggleEasing(false);

                    current = max = min = 0;

                    initial = event.touches[0].clientX;

                    this.setAttribute('was_touched', 1);

                    timeout = setTimeout(function() {
                        elem.removeAttribute('was_touched');
                    }, 400);
                });

                this.gallery.addEventListener('touchmove', function(event) {
                    current = initial - event.touches[0].clientX;

                    if (max < current) {
                        max = current;
                    }

                    if (min > current) {
                        min = current;
                    }

                    if (self.currentIndex == 0 && current < 0) {
                        current = 0;
                        initial = event.touches[0].clientX;
                    } else if (self.currentIndex == (self.images.length - 1) && current > 0) {
                        current = 0;
                        initial = event.touches[0].clientX;
                    }

                    self.galleryWrapper.setStyle('transform', 'translate(' + (-1 * (self.offset + current)) + 'px)');
                });

                this.gallery.addEventListener('touchend', function(event) {
                    self.toggleEasing(true);

                    if (Math.abs(current) < 20) {
                        return;
                    }

                    if (current >= max) {
                        self.showImage(self.currentIndex + 1);
                    } else if (current <= min) {
                        self.showImage(self.currentIndex - 1);
                    } else {
                        self.showImage();
                    }
                });

                this.gallery.addEventListener('mouseover', function() {
                    if (!this.getAttribute('was_touched')) {
                        self.dom.addClass(self.controls, 'has-pointer');
                    }
                });

                this.dom.getElement('.gallery-fullscreen-button', this.controls).addEventListener('click', function() {
                    self.toggleFullscreen();
                });

                var bullets = this.dom.getElements('.gallery-bullet');
                for (i = 0; i < bullets.length; i++) {
                    bullets[i].addEventListener('click', function(event) {
                        var index = this.getAttribute('data-index');
        
                        self.showImage(index);
                    });

                }

                var leftRight = this.dom.getElements('.gallery-left,.gallery-right', this.controls);
                for (var i = 0; i < leftRight.length; i++) {
                    leftRight[i].addEventListener('click', function(e) {
                        var direction = self.dom.hasClass(this, 'gallery-left');

                        if (direction) {
                            self.showImage(self.currentIndex - 1);
                        } else {
                            self.showImage(self.currentIndex + 1);
                        }
                    });
                }

                document.addEventListener('keyup', function(event) {
                    switch (event.keyCode) {
                        case 39:
                            self.showImage(self.currentIndex + 1);
                            break;

                        case 37:
                            self.showImage(self.currentIndex - 1);
                            break;

                        case 70:
                            self.toggleFullscreen();
                            break;

                        case 27:
                            self.toggleFullscreen(false);
                            break;
                    }
                });

                window.addEventListener('resize', function() {
                    self.setSizes();
                });
                                                                                                                                                                                                                                                        ;
                this.dom.addClass(this.controls, 'done');
            }
        },

        toggleFullscreen: function(toggle) {
            this.toggleEasing(false);

            if (typeof toggle == 'undefined') {
                toggle = !this.dom.hasClass(this.gallery, 'fullscreen');
            }

            if (toggle) {
                this.dom.addClass(this.gallery, 'fullscreen');

                if (!this.fullscreenDone) {
                    // set Images to max size for best quality, no matter what the screen size
                    this.setImages('xl');
                    this.fullscreenDone = true;
                }
            } else {
                this.dom.removeClass(this.gallery, 'fullscreen');
            }

            this.setSizes();
        },

        toggleEasing: function(on) {
            if (on) {
                this.dom.addClass(this.galleryWrapper, 'easing');
            } else {
                this.dom.removeClass(this.galleryWrapper, 'easing');
            }

        },

        showImage: function(index) {
            var i, bullets = this.dom.getElements('.gallery-bullet', this.controls);

            if (typeof index == 'undefined') {
                index = this.currentIndex;
            }

            if (!this.images[index]) {
                return;
            }

            var wrapper = this.images[index],
                maxOffset = this.galleryWrapper.clientWidth - this.gallery.clientWidth;

            if (wrapper.offsetLeft >= maxOffset) {
                this.offset = maxOffset;
            } else {
                this.offset = wrapper.offsetLeft;
            }

            this.currentIndex = index;

            this.galleryWrapper.style.transform = 'translate(' + (-1 * this.offset) + 'px)';

            var wrappers = this.dom.getElements('.gallery-image-wrapper', this.galleryWrapper);
            for (i = 0; i < wrappers.length; i++) {
                this.dom.removeClass(wrappers[i], 'active');
            }

            this.dom.addClass(wrapper, 'active');

            if (bullets.length) {
                for (i = 0; i < bullets.length; i++) {
                    this.dom.removeClass(bullets[i], 'active');
                }

                this.dom.addClass(bullets[index], 'active');
            }

            var i = 0,
                controls = this.dom.getElements('div.gallery-control', this.controls);

            for (i; i < controls.length; i++) {
                this.dom.addClass(controls[i], 'active');

                if ((this.currentIndex === 0 && i === 0) || (this.currentIndex == (this.images.length - 1) && i == 1)) {
                    this.dom.removeClass(controls[i], 'active');
                }
            }
        },


        getFullscreenPrefixes: function() {
            var i, self = this,
                fullscreenMethodNames = [
                    'webkitRequestFullscreen',
                    'mozRequestFullScreen',
                    'msRequestFullscreen',
                    'requestFullscreen'
                ],
                exitMethodNames = [
                    'webkitExitFullscreen',
                    'mozCancelFullScreen',
                    'msExitFullscreen',
                    'exitFullscreen'
                ],
                eventNames = [
                    'webkitfullscreenchange',
                    'mozfullscreenchange',
                    'MSFullscreenChange',
                    'fullscreenchange'
                ],
                fullscreenElements = [
                    'webkitFullscreenElement',
                    'mozFullScreenElement',
                    'msFullscreenElement',
                    'fullscreenElement'
                ];

            for (i = 0;  i < fullscreenMethodNames.length; i++) {
                var check = fullscreenMethodNames[i];

                if (self.gallery[check]) {
                    self.fullscreenAPI = {
                        'requestFullscreen': check,
                        'exitFullscreen': exitMethodNames[i],
                        'event': eventNames[i],
                        'fullscreenElement': fullscreenElements[i]
                    };

                    return true;
                }
            }

            return false;
        }
    };
})(window, document);
