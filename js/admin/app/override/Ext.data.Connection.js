/**
 * This override is for injection the ability to detect and handle php-errors found in XHR response text
 */
Ext.override(Ext.data.Connection, {

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
            phpErrors;

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
                    title: 'System error',
                    msg: err.join('<br><br>'),
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        }

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