/**
 *
 */

jQuery.noConflict();

jQuery( document ).ready(function( $ ) {

	// OBJECTS

	$("table.tef_fields_table tbody").sortable({
		handle: ".sortable-icon",
		update: function(event, ui){
			tef_actualize_and_save_field_positions();
		}
	}).disableSelection();

	$("form.field-form .options_list .options").sortable({
		handle: ".option",
		update: function(event, ui){
			//tef_actualize_and_save_field_positions();
		}
	}).disableSelection();

	// ITERATORS

	$('tr', 'table.tef_fields_table').each(function () {
        $(this).width( $(this).width() );
    });

	$('td:not(.hidden), th:not(.hidden)', 'table.tef_fields_table').each(function () {
        $(this).width( $(this).width() );
    });

	// EVENTS
	$('#tef-admin').on('change', 'form.field-form input, form.field-form select, form.field-form textarea', tef_enable_form);

	$('#tef-admin').on('submit', 'form.field-form', tef_save_field);

	$('#tef-admin').on('click', '.tef_fields_table .row-actions .edit a, .tef_fields_table a.row-title', tef_set_row_in_edition);

	$('#tef-admin').on('click', '.tef_fields_table .row-actions .delete a', tef_delete_field);

	$('#tef-admin').on('click', 'a.unlock-field', tef_unlock_field);

	$('#tef-admin').on('click', '.add-new-field', tef_add_new_field);

	$('#tef-admin').on('click', 'form.field-form button[name=cancel]', tef_restore_form_field);

	$('#tef-admin').on('change', 'form.field-form select[name=type]', function(){
		var type = $(this).val();

		$(this).closest('form.field-form').find('table.tef-form tbody tr').each(function(index, elem){

			if( typeof $(this).data('for') != "undefined" ){
				var types = $(this).data('for').split(" ");

				if(jQuery.inArray( type, types ) != -1)
					$(this).removeClass('no-display');
				else
					$(this).addClass('no-display');

			}

		});


	});

	$('#tef-admin').on('click', 'input.split_options', function(){
		var form = $(this).closest('form.field-form');

		if($(this).is(':checked')){
			form.find('.options_list .option .key').removeClass('no-display');
		}else{
			form.find('.options_list .option .key').addClass('no-display');
		}

	});

	$('#tef-admin').on('change', 'form.field-form .options_list .option input', function(){

		var input_key = $(this).parent('.option').find('input.key'),
			input_value = $(this).parent('.option').find('input.value');

		if( $(this).parent('.option').is(':last-child') ){

			if(input_key.val() != "" || input_value.val() != ""){

				var row =  $(this).parent('.option'),
					row_clone = row.clone();

				row_clone.find('input').val("");

				$(this).closest('.options').append( row_clone );

			}

		}else{

			if(input_key.val() == "" && input_value.val() == ""){

				$(this).parent('.option').fadeOut(300, function(){
					$(this).remove();
				});

			}

		}


	});

  $('body').on('click','button.tef-media-button', function(){
    var frame,
        media_button = $(this),
        input = media_button.siblings('input.tef-media-value'),
        type = media_button.data('type'),
        img_container = media_button.parent().find('.tef-media-file'),
        img = img_container.find('img');

        // Create a new media frame
        frame = wp.media({
          title: 'Select or Upload Media Of Your Chosen Persuasion',
          button: {
            text: 'Use this media'
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media frame...
        frame.on( 'select', function() {

          // Get media attachment details from the frame state
          var attachment = frame.state().get('selection').first().toJSON();

          console.log( frame.state().get('selection').first().toJSON() );

          if(0 < img.length){
        	  console.log( attachment.id );
            if(type == "image"){
        	    img.attr('src', attachment.url);
            }else if(type == "file"){
              img.attr('src', attachment.icon);
              img.siblings('span').html(attachment.name);
            }
            img.parent('a').attr('href',attachment.url);

        	  img_container.removeClass('no-display');
          }

          input.val( attachment.id );

          media_button.html( tef.translations.button.replace );

        });

        // Finally, open the modal on click
        frame.open();

  });


	/**
	 *
	 */
	function tef_restore_form_field(){

		var form =  $(this).closest('form'),
			container = form.closest('tr'),
			label_col = container.find('td.label'),
			json_value = form.find('input[name=object]').val(),
			data = JSON.parse( json_value );


		if(0 != form.find('input[name=ID]').val()){

			tef_row_actualize(container, data);
			container.removeClass('in-edition');
			label_col.removeAttr('colspan');

			// Display or no the no-items row
			tef_display_no_items_row();

		}else{
			container.removeClass('in-edition').fadeOut(500, function(){
				$(this).remove();

				// Actualize positions.
				tef_actualize_fields_positions();

				// Display or no the no-items row
				tef_display_no_items_row();
			});

		}



	}

	/**
	 *
	 */
	function tef_delete_field(event){

		event.preventDefault();

		var tr = $(this).closest('tr'),
			form = tr.find('form.field-form');

		var n = noty({
			layout: 'center',
			//timeout: 1000,
			text: tef.translations.msg.confirm_delete,
			type: 'alert',
			 dismissQueue: true,
		    modal: true,
			animation: {
		        open: 'animated pulse', // Animate.css class names
		        close: 'animated fadeOut', // Animate.css class names
		        easing: 'swing', // unavailable - no need
		        speed: 200, // unavailable - no need
		    },
		    buttons: [
			   {
				   addClass: 'button button-primary', text: tef.translations.msg.accept, onClick: function ($noty) {

					   jQuery.ajax({
							method: 'POST',
							url: ajaxurl,
							data: {
								action: 'tef_delete_field',
								form: form.serialize(),
							},
							success: function(result){

								if(result != 0){

									$noty.close();

									tr.fadeOut(500, function(){
										$(this).remove();

										tef_actualize_and_save_field_positions();

										tef_display_no_items_row();

									});
								}else{

									 $noty.close();

									console.error(result);
								}
							},
							error: function(){
								console.error('ERROR!');
							}
						});

				   }
			   },
			   {
				   addClass: 'button button-danger', text: tef.translations.msg.cancel, onClick: function ($noty) {
					   $noty.close();
			       }
			   },
		   	],

		});


	}

	/**
	 *
	 */
	function tef_enable_form(){
		var form = $(this).closest('form.field-form'),
			submit_button = form.find('button[name=save]');

		submit_button.removeAttr('disabled');
	}

	/**
	 *
	 */
	function tef_actualize_and_save_field_positions(){
		// Actualize positions
		tef_actualize_fields_positions();

		// Save positions
		tef_save_fields_positions();
	}

	/**
	 *
	 */
	function tef_actualize_fields_positions(){

		var i = 1;
		$('#tef-admin table.tef_fields_table > tbody > tr:not(.no-items)').each(function(index, elem) {

			var container = $(elem),
				input_field = container.find('input[name^=field]'),
				form = container.find('form.field-form'),
				input_ID = form.find('input[name=ID]'),
				input_position = form.find('input[name=position]');

			input_field.val(i);
			input_position.val( i++ );

		});

		// Actualize prox field position
		tef_actualize_default_field_position( i );

	}

	/**
	 *
	 */
	function tef_display_no_items_row(){

		console.log( $('#tef-admin table.tef_fields_table > tbody > tr:not(.no-items)').length );

		if(0 == $('#tef-admin table.tef_fields_table > tbody > tr:not(.no-items)').length){
			if( $('#tef-admin table.tef_fields_table > tbody > tr.no-items').length )
				$('#tef-admin table.tef_fields_table > tbody > tr.no-items').fadeIn(500);
			else{

				jQuery.ajax({
					method: 'POST',
					url: ajaxurl,
					data: {
						action: 'tef_get_no_items_row'
					},
					success: function(result){
						if(result != 0){

							console.log( result );

							row = jQuery.parseHTML( result );

							$(row).css({'display':'none'});

							$('#tef-admin table.tef_fields_table > tbody').append( row );

							$(row).fadeIn(500);

						}else
							console.error( result );
					},
					error: function(error){
						console.error(error);
					}
				});
			}

		}else{

			if( $('#tef-admin table.tef_fields_table > tbody > tr.no-items').is(':visible') )
				$('#tef-admin table.tef_fields_table > tbody > tr.no-items').fadeOut(500);

		}

	}

	/**
	 *
	 */
	function tef_save_fields_positions(){

		var form = $('#tef-admin form#fields-positions');

		jQuery.ajax({
			method: 'POST',
			url: ajaxurl,
			data: {
				action: 'tef_save_fields_positions',
				form: form.serialize(),
			},
			success: function(result){
				if(result != 0){

					var n = noty({
						layout: 'topRight',
						timeout: 1000,
						text: tef.translations.msg.saved,
						type: 'success',
						closeWith: ['click','hover', 'backdrop'],
						animation: {
					        open: 'animated tada', // Animate.css class names
					        close: 'animated hinge', // Animate.css class names
					        easing: 'swing', // unavailable - no need
					        speed: 500, // unavailable - no need
					    },
					});

				}else
					console.error( result );
			},
			error: function(error){
				console.error(error);
			}
		});

	}

	/**
	 *
	 */
	function tef_actualize_default_field_position( i ){

		i = parseInt( i );

		$('#tef-admin form#defaults input[name=position]').val( i );


	}

	/**
	 *
	 */
	function tef_row_actualize( row, data ){

		var form = $(row).find('form.field-form');

		row.find('td.ID').html(data.ID);
		form.find('input[name="ID"]').val( data.ID );

		row.find('td.taxonomy').html(data.taxonomy);
		form.find('input[name="taxonomy"]').val( data.taxonomy );

		row.find('td.position input').attr('name','field['+data.ID+']').val(data.position);
		form.find('input[name="position"]').val( data.position );

		row.find('td.label span.label').html(data.label);
		form.find('input[name="label"]').val( data.label );

		if(data.required){
			if(0 == row.find('td.label a.row-title span.required').length){
				row.find('td.label a.row-title').append('<span class="required">*</span>');

			}
			form.find('input[name="required"]').attr('checked','checked');
		}else{
			row.find('td.label a.row-title span.required').remove();
			form.find('input[name="required"]').removeAttr('checked');
		}
		row.find('td.required').html(data.required);


		row.find('td.name').html(data.name);
		form.find('input[name="name"]').val( data.name );

		row.find('td.type').html(tef.translations.types[data.type]);
		form.find('select[name="type"] option[value="'+data.type+'"]').attr('selected','selected');

		row.find('td.description').html(data.description);
		form.find('textarea[name="description"]').val( data.description );

		form.find('input[name="object"]').val( JSON.stringify(data) );
	}

	/**
	 *
	 */
	function tef_save_field(event){
		event.preventDefault();
		var form = $(this),
			container = $(this).closest('tr'),
			label_col = container.find('td.label');

		jQuery.ajax({
			method: 'POST',
			url: ajaxurl,
			data: {
				action: 'tef_save_field',
				form: form.serialize(),
			},
			success: function(result){

				if(result != 0){
					tef_row_actualize(container, JSON.parse(result) );
					container.removeClass('in-edition');
					label_col.removeAttr('colspan');

					var n = noty({
						layout: 'topRight',
						timeout: 1000,
						text: tef.translations.msg.saved,
						type: 'success',
						closeWith: ['click','hover', 'backdrop'],
						animation: {
					        open: 'animated tada', // Animate.css class names
					        close: 'animated hinge', // Animate.css class names
					        easing: 'swing', // unavailable - no need
					        speed: 500, // unavailable - no need
					    },
					});

				}else
					console.error(result);
			},
			error: function(){
				console.error('ERROR!');
			}
		});

	}

	/**
	 *
	 */
	function tef_unlock_field(){

		var elem = $(this);

		if( elem.siblings('input[readonly=readonly]').length ){

			var n = noty({
				layout: 'center',
				//timeout: 1000,
				text: tef.translations.msg.confirm,
				type: 'alert',
				 dismissQueue: true,
			    modal: true,
				animation: {
			        open: 'animated pulse', // Animate.css class names
			        close: 'animated fadeOut', // Animate.css class names
			        easing: 'swing', // unavailable - no need
			        speed: 200, // unavailable - no need
			    },
			    buttons: [
				   {
					   addClass: 'button button-primary', text: tef.translations.msg.accept, onClick: function ($noty) {
						   $noty.close();
						   $(elem).siblings('input[readonly=readonly]').removeAttr('readonly').focus();
					   }
				   },
				   {
					   addClass: 'button button-danger', text: tef.translations.msg.cancel, onClick: function ($noty) {
						   $noty.close();
				       }
				   },
			   	],

			});

		}
			//
	}


	/**
	 *
	 */
	function tef_set_row_in_edition(event){
		event.preventDefault();

		var container = $(this).closest('tr'),
			label_col = container.find('td.label');

		container.addClass('in-edition');

		label_col.attr('colspan',4);

	}

	/**
	 *
	 */
	function tef_add_new_field(event){

		event.preventDefault();



		var data = $('form#defaults');

		jQuery.ajax({
			method: 'POST',
			url: ajaxurl,
			data: {
				action: 'tef_get_row_template',
				data: data.serialize(),
			},
			success: function(result){

				if( result ){
					var html = jQuery.parseHTML( result );

					$('table.tef_fields_table > tbody').append( html );

					$('.row-actions .edit a', html).click();

					$('input[name=label]',html).focus();

					// Ocult no items row
					tef_display_no_items_row();

					// Actualize positions
					tef_actualize_fields_positions();
				}

			},
			error: function(){
				console.error('ERROR!');
			}
		});

	}

});
