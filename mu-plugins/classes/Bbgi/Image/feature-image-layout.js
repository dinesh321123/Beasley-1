( function( $ ) {

	var Drawer = function() {

	}

	Drawer.prototype = {

		enable: function() {
			this.getEditButton().on(
				'click', $.proxy( this.didEditClick, this )
			);

			this.getSaveButton().on(
				'click', $.proxy( this.didSaveClick, this )
			);

			this.getCancelButton().on(
				'click', $.proxy( this.didCancelClick, this )
			);
		},

		didEditClick: function() {
			this.savedLayout = this.getOutputField().val();
			this.getChoiceGroup().val( [ this.savedLayout ] );
			this.open();

			return false;
		},

		didSaveClick: function() {
			var choiceField = this.getCheckedChoiceField();
			var choice      = choiceField.val();
			var choiceLabel = choiceField.data( 'label' );

			this.savedLayout = choice;
			this.getSelectedChoice().text( choiceLabel );
			this.close();
			this.export( choice );

			return false;
		},

		didCancelClick: function() {
			this.export( this.savedLayout );
			this.close();

			return false;
		},

		open: function() {
			this.getEditButton().hide();
			this.getDrawer().slideDown();
		},

		close: function() {
			this.getEditButton().show();
			this.getDrawer().slideUp();
		},

		export: function( choice ) {
			this.getOutputField().val( choice );
		},

		getEditButton: function() {
			return $( '.edit-feature-image-layout' );
		},

		getSaveButton: function() {
			return $( '.button-save-image-layout' );
		},

		getCancelButton: function() {
			return $( '.button-cancel-image-layout' );
		},

		getOutputField: function() {
			return $( 'input[name=feature_image_layout]' );
		},

		getChoiceGroup: function() {
			return $( 'input[name=feature_layout_choice]' );
		},

		getCheckedChoiceField: function() {
			return $( 'input[name=feature_layout_choice]:checked' );
		},

		getSelectedChoice: function() {
			return $( '.selected-feature-image-layout' );
		},

		getDrawer: function() {
			return $( '#feature-image-layout-drawer' );
		}

	}

	$( document ).ready( function() {
		var drawer = new Drawer();
		drawer.enable();
	} );

} )( jQuery );
