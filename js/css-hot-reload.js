(function (Drupal, drupalSettings, once) {
    'use strict';

    Drupal.behaviors.cssHotReload = {
        attach: function (context) {
            once('css-hot-reload', 'html', context).forEach(function () {
                var interval = (drupalSettings.cssHotReload && drupalSettings.cssHotReload.interval) || 1000;
                var known = {};

                function getLinks() {
                    return Array.prototype.slice.call(
                        document.querySelectorAll('link[rel="stylesheet"]')
                    );
                }

                function reloadStylesheet(link) {
                    var baseUrl = link.href.split('?')[0];
                    var newLink = link.cloneNode();
                    newLink.href = baseUrl + '?_hr=' + Date.now();

                    newLink.onload = function () {
                        link.remove();
                    };
                    link.parentNode.insertBefore(newLink, link.nextSibling);
                }

                function poll() {
                    var links = getLinks();
                    var hrefs = links
                        .map(function (link) { return link.getAttribute('href'); })
                        .filter(Boolean);

                    if (!hrefs.length) return;

                    fetch('/css-hot-reload/check?paths=' + encodeURIComponent(hrefs.join(',')))
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            links.forEach(function (link) {
                                var href = link.getAttribute('href');
                                var mtime = data[href];
                                if (mtime === undefined) return;

                                if (known[href] === undefined) {
                                    known[href] = mtime;
                                    return;
                                }
                                if (known[href] !== mtime) {
                                    known[href] = mtime;
                                    reloadStylesheet(link);
                                }
                            });
                        })
                        .catch(function () {});
                }

                setInterval(poll, interval);
            });
        }
    };
})(Drupal, drupalSettings, once);