/**
 * Radio image control.
 *
 * Stores the control's constructor on wp.customize.kiaRadioImageControl. The PHP class registers it
 * for its own control type straight after this file loads.
 *
 * @version 2.0.0
 */
( function( $, api ) {

	api.kiaRadioImageControl = api.Control.extend(
		{

			ready: function() {
				var control = this;

				this.container.on(
					'change',
					'input:radio',
					function() {
						control.setting.set( $( this ).val() );
					}
				);

			}
		}
	);

} )( jQuery, wp.customize );
