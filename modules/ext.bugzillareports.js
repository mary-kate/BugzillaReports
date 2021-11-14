$( function() {
	$( 'div.bz_comment' ).hide();
	$( 'tr.bz_bug' ).on( {
		'mouseenter': function () {
			$( this ).find( 'td div.bz_comment' ).show();
		},
		'mouseleave': function () {
			$( this ).find( 'td div.bz_comment' ).hide();
		}
	} );
} );
