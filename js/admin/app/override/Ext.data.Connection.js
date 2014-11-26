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
            }, doc, contentNode,
            phpErrors, responseHtml;

        try {
            doc = frame.contentWindow.document || frame.contentDocument || window.frames[frame.id].document;
            if (doc) {
                if (doc.body) {

                    phpErrors = me.phpErrors(doc.body.innerHTML);
                    if (phpErrors.length) {
                        options.success = false;
                        var err = me.errorExplorer(phpErrors);

                        // Write php-errors to the console, additionally
                        if (console && (console.log || console.error))
                            for (var i in err) console[console.error ? 'error' : 'log'](err[i]);

                        Ext.Msg.show({
                            title: 'Server error',
                            msg: err.join('<br><br>'),
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }

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

        me.fireEvent('requestcomplete', me, response, options);

        Ext.callback(options.success, options.scope, [response, options]);
        Ext.callback(options.callback, options.scope, [options, true, response]);

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
            response,
            phpErrors,
            json;

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
            phpErrors = me.phpErrors(request.xhr.responseText);
            if (phpErrors.length) {
                success = false;
                var err = me.errorExplorer(phpErrors);

                // Write php-errors to the console, additionally
                if (console && (console.log || console.error))
                    for (var i in err) console[console.error ? 'error' : 'log'](err[i]);

                /*if (console) for (var i in err) Ext.Error.raise({
                    msg: err[i],
                    option: {adasd: 'hello'}
                });*/

                Ext.Msg.show({
                    title: 'Server error',
                    msg: err.join('<br><br>'),
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });

            // Else if responseText can possibly be a json-encoded string
            } else if (request.xhr.responseText.substr(0, 1).match(/[{\[]/)
                && typeof (json = Ext.JSON.decode(request.xhr.responseText, true)) == 'object'
                && json.hasOwnProperty('success')) {
                success = json.success;
                if (json.hasOwnProperty('msg')) {
                    Ext.Msg.show({
                        title: Indi.lang.I_ERROR,
                        msg: json.msg,
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg[json.success ? 'INFO' : 'WARNING'],
                        modal: true
                    });
                }
            }
        }

        // If still success
        if (success) {
            response = me.createResponse(request);
            me.fireEvent('requestcomplete', me, response, options);
            Ext.callback(options.success, options.scope, [response, options]);
        } else {
            if (result.isException || request.aborted || request.timedout) {
                response = me.createException(request);
            } else {
                response = me.createResponse(request);
            }
            me.fireEvent('requestexception', me, response, options);
            Ext.callback(options.failure, options.scope, [response, options]);
        }
        Ext.callback(options.callback, options.scope, [options, success, response]);
        delete me.requests[request.id];
        return response;
    },

    /**
     * Detect error messages, encapsulated with <error/> tag, within the raw responseText
     *
     * @param rt Response text, for trying to find errors in
     * @return {Array} Found errors
     */
    phpErrors: function(rt){

        // If response text is empty - return false
        if (!rt.length) return ['Empty response'];

        // Define variables
        var errorA = [], errorI;

        // Find an parse errors
        Indi.fly('<response>'+rt+'</response>').select('error').each(function(item){
            if (errorI = Ext.JSON.decode(item.getHTML(), true)) errorA.push(errorI);
        });

        // Return errors
        return errorA;
    },

    /**
     * Builds a string representation of a given errors, suitable for use as Ext.MessageBox contents
     *
     * @param errorOA
     * @param asStringsArray
     * @return {Array}
     */
    errorExplorer: function(errorOA, asStringsArray) {

        // Define auxilliary variables
        var errorSA = [], typeO = {1: 'Fatal error', 2: 'Warning', 4: 'Parse error'};

        // Convert each error message object to a string
        for (var i = 0; i < errorOA.length; i++)
            errorSA.push('PHP ' + typeO[errorOA[i].code] + ': ' + errorOA[i].text + ' at ' +
                errorOA[i].file + ' on line ' + errorOA[i].line);

        // Return error strings array
        return errorSA;
    }
});