/**
 * This override is for injection the ability to detect and handle php-errors found in XHR response text
 */
Ext.override(Ext.data.Connection, {

    /**
     * @private
     * Callback handler for the upload function. After we've submitted the form via the iframe this creates a bogus
     * response object to simulate an XHR and populates its responseText from the now-loaded iframe's document body
     * (or a textarea inside the body). We then clean up by removing the iframe
     */
    onUploadComplete: function(frame, options) {
        var me = this,
        // bogus response object
            response = {
                responseText: '',
                responseXML: null
            }, doc, contentNode;

        try {
            doc = frame.contentWindow.document || frame.contentDocument || window.frames[frame.id].document;
            if (doc) {
                if (doc.body) {

                    // Response sent as Content-Type: text/json or text/plain. Browser will embed in a <pre> element
                    // Note: The statement below tests the result of an assignment.
                    if ((contentNode = doc.body.firstChild) && /pre/i.test(contentNode.tagName)) {
                        response.responseText = contentNode.innerText;
                    }

                    // Response sent as Content-Type: text/html. We must still support JSON response wrapped in textarea.
                    // Note: The statement below tests the result of an assignment.
                    else if (contentNode = doc.getElementsByTagName('textarea')[0]) {
                        response.responseText = contentNode.value;
                    }
                    // Response sent as Content-Type: text/html with no wrapping. Scrape JSON response out of text
                    else {
                        response.responseText = doc.body.textContent || doc.body.innerText;
                    }
                }
                //in IE the document may still have a body even if returns XML.
                response.responseXML = doc.XMLDocument || doc;
            }
        } catch (e) {
        }

        var success = Indi.parseResponse(response, options);

        if (success) {
            me.fireEvent('requestcomplete', me, response, options);
            Ext.callback(options.success, options.scope, [response, options]);
        } else {
            me.fireEvent('requestexception', me, response, options);
            Ext.callback(options.failure, options.scope, [response, options]);
        }

        Ext.callback(options.callback, options.scope, [options, success, response]);

        setTimeout(function() {
            Ext.removeNode(frame);
        }, 100);
    },

    /**
     * To be called when the request has come back from the server
     * @private
     * @param {Object} request
     * @return {Object} The response
     */
    onComplete : function(request) {
        var me = this,
            options = request.options,
            result,
            success,
            response;

        try {
            result = me.parseStatus(request.xhr.status);
        } catch (e) {
            // in some browsers we can't access the status if the readyState is not 4, so the request has failed
            result = {
                success : false,
                isException : false
            };
        }
        success = result.success;

        if (success) {
            response = me.createResponse(request);
            Indi.parseResponse(response, options);
            me.fireEvent('requestcomplete', me, response, options);
            Ext.callback(options.success, options.scope, [response, options]);
        } else {
            if (result.isException || request.aborted || request.timedout) {
                response = me.createException(request);
            } else {
                response = me.createResponse(request);
                Indi.parseResponse(response, options);
            }
            me.fireEvent('requestexception', me, response, options);
            Ext.callback(options.failure, options.scope, [response, options]);
        }
        Ext.callback(options.callback, options.scope, [options, success, response]);
        delete me.requests[request.id];
        return response;
    }
});

// Small override for Ext.Msg
Ext.override(Ext.Msg, {
    jflushFn: 'show',
    msgCt: null,
    side: function(cfg){
        if (Ext.isString(cfg) && !cfg.length) return;
        if (Ext.isObject(cfg) && (!cfg.body || !cfg.body.length)) return;
        if (!this.msgCt) this.msgCt = Ext.DomHelper.insertFirst(document.body, {id:'i-notice-div'}, true);
        var m = Ext.DomHelper.append(this.msgCt, '<div class="x-window-default i-notice">' +
            '<img src="'+Indi.std+'/i/admin/btn-icon-close-side.png" class="i-notice-close">' +
            (Ext.isObject(cfg) && cfg.header ? '<h1>' + cfg.header + '</h1>' : '') +
            '<p>' + (Ext.isString(cfg) ? cfg : (cfg.body || cfg.msg)).replace(/\[/g, '<').replace(/\]/g, '>') + '</p>' +
        '</div>', true);

        // Add handler for close-icon
        m.down('.i-notice-close').on('click', function(e, dom){
            Ext.get(dom).up('.i-notice').fadeOut({remove: true});
        });

        // Add handler for jump-links
        m.select('[jump]').each(function(el){
            el.on('click', function(e, dom){
                Indi.load(Ext.get(dom).attr('jump') + 'jump/1/');
                Ext.get(dom).up('.i-notice').fadeOut({remove: true});
            });
        });
    }
});
