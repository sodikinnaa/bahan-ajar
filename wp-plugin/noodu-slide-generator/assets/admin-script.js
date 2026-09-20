( function () {
	'use strict';

	var t = ( window.nooduData && window.nooduData.i18n ) || {};

	function api( path, options ) {
		options = options || {};
		options.headers = options.headers || {};
		options.headers['X-WP-Nonce'] = window.nooduData.nonce;
		options.credentials = 'same-origin';

		return fetch( window.nooduData.restUrl + path, options ).then( function ( response ) {
			return response.json().then( function ( body ) {
				if ( ! response.ok ) {
					throw new Error( body.message || 'HTTP ' + response.status );
				}
				return body;
			} );
		} );
	}

	function notice( message, type ) {
		var box = document.getElementById( 'noodu-notice' );
		if ( ! box ) {
			window.alert( message );
			return;
		}
		box.className = 'notice notice-' + ( type || 'success' );
		box.querySelector( 'p' ).textContent = message;
		box.style.display = 'block';
		box.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
	}

	function fetchModels( button ) {
		button.disabled = true;
		button.textContent = t.fetching || 'Fetching…';

		api( 'models' )
			.then( function ( data ) {
				var select = document.getElementById( 'noodu-model' );
				var current = select.value;

				select.innerHTML = '';

				if ( ! data.models.length ) {
					notice( t.noModels || 'No models returned.', 'warning' );
					return;
				}

				var placeholder = document.createElement( 'option' );
				placeholder.value = '';
				placeholder.textContent = t.selectModel || 'Select a model…';
				select.appendChild( placeholder );

				data.models.forEach( function ( model ) {
					var option = document.createElement( 'option' );
					option.value = model.id;
					option.textContent = model.id;
					select.appendChild( option );
				} );

				if ( current ) {
					select.value = current;
				}

				notice( data.models.length + ' ' + ( t.modelsFound || 'models found.' ) );
			} )
			.catch( function ( error ) {
				notice( error.message, 'error' );
			} )
			.finally( function () {
				button.disabled = false;
				button.textContent = t.fetchModels || 'Fetch available models';
			} );
	}

	function generate( button ) {
		var name = document.getElementById( 'noodu-project-name' ).value.trim();
		var model = document.getElementById( 'noodu-model' ).value;
		var prompt = document.getElementById( 'noodu-prompt' ).value.trim();
		var reference = document.getElementById( 'noodu-reference' );

		if ( ! name || ! model || ! prompt ) {
			notice( t.fillFields || 'Please fill in all fields.', 'error' );
			return;
		}

		var form = new FormData();
		form.append( 'project_name', name );
		form.append( 'model', model );
		form.append( 'prompt', prompt );
		if ( reference && reference.files[0] ) {
			form.append( 'reference_file', reference.files[0] );
		}

		button.disabled = true;
		button.textContent = t.generating || 'Generating…';

		api( 'generate', { method: 'POST', body: form } )
			.then( function ( data ) {
				notice( ( t.done || 'Deck generated.' ) + ' ' + data.slide_count + ' slides.' );

				var result = document.getElementById( 'noodu-result' );
				if ( result ) {
					result.style.display = 'block';
					result.innerHTML = '';

					var open = document.createElement( 'a' );
					open.className = 'button button-primary';
					open.href = data.download_url;
					open.target = '_blank';
					open.rel = 'noopener';
					open.textContent = data.is_pdf ? 'Open PDF' : 'Open deck';

					var list = document.createElement( 'a' );
					list.className = 'button';
					list.href = window.nooduData.projectsUrl;
					list.textContent = 'All projects';

					result.appendChild( open );
					result.appendChild( document.createTextNode( ' ' ) );
					result.appendChild( list );
				}

				document.getElementById( 'noodu-prompt' ).value = '';
				document.getElementById( 'noodu-project-name' ).value = '';
				if ( reference ) {
					reference.value = '';
				}
			} )
			.catch( function ( error ) {
				notice( error.message, 'error' );
			} )
			.finally( function () {
				button.disabled = false;
				button.textContent = t.generate || 'Generate slides';
			} );
	}

	function remove( button ) {
		if ( ! window.confirm( t.confirmDelete || 'Delete this project?' ) ) {
			return;
		}

		button.disabled = true;

		api( 'projects/' + button.dataset.id, { method: 'DELETE' } )
			.then( function () {
				var row = button.closest( 'tr' );
				if ( row ) {
					row.remove();
				}
			} )
			.catch( function ( error ) {
				button.disabled = false;
				notice( error.message, 'error' );
			} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var fetchButton = document.getElementById( 'noodu-fetch-models' );
		if ( fetchButton ) {
			fetchButton.addEventListener( 'click', function () {
				fetchModels( this );
			} );
		}

		var generateButton = document.getElementById( 'noodu-generate' );
		if ( generateButton ) {
			generateButton.addEventListener( 'click', function () {
				generate( this );
			} );
		}

		document.querySelectorAll( '.noodu-delete' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				remove( this );
			} );
		} );
	} );
}() );
