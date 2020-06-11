$.widget( "ui.dialog", $.ui.dialog, {
	_allowInteraction: function( event ) {
		return !!$( event.target ).closest( ".other-popups" ).length || this._super( event );
	}
});