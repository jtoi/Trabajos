console.log('Cookie blocked');
if(!document.__defineGetter__) {
    Object.defineProperty(document, 'cookie', {
        get: function(){ console.log('Cookie blocked'); return ''; },
        set: function(){ console.log('Cookie blocked'); return '' },
    });
} else {
    document.__defineGetter__("cookie", function() { console.log('Cookie blocked'); return '';} );
    document.__defineSetter__("cookie", function() {console.log('Cookie blocked'); } );
}