console.log('Cookie enabled');
if(!document.__defineGetter__) {
    Object.defineProperty(document, 'cookie', {
        get: function(){ console.log('Cookie enabled'); return this._value; },
        set: function(val){ console.log('Cookie enabled'); this._value = val; return this._value; },
    });
} else {
    document.__defineGetter__("cookie", function() { console.log('Cookie enabled'); return this._value;} );
    document.__defineSetter__("cookie", function(val) {console.log('Cookie enabled'); this._value = val; return this._value; } );
}