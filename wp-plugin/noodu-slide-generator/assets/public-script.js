( function () {
	'use strict';

	var t = ( window.nooduData && window.nooduData.i18n ) || {};
	var app;

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
		var box = app.querySelector( '.noodu-notice' );
		box.className = 'noodu-notice noodu-notice--' + ( type || 'success' );
		box.textContent = message;
		box.style.display = 'block';
	}

	function fetchModels( button ) {
		button.disabled = true;
		button.textContent = t.fetching || 'Fetching…';

		api( 'models' )
			.then( function ( data ) {
				var select = app.querySelector( '#noodu-model' );
				var current = select.value;

				select.innerHTML = '';

				if ( ! data.models.length ) {
					notice( t.noModels || 'No models returned.', 'error' );
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
		var name = app.querySelector( '#noodu-project-name' ).value.trim();
		var model = app.querySelector( '#noodu-model' ).value;
		var prompt = app.querySelector( '#noodu-prompt' ).value.trim();
		var reference = app.querySelector( '#noodu-reference' );

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

		var loading = app.querySelector( '.noodu-loading' );

		button.disabled = true;
		button.textContent = t.generating || 'Generating…';
		loading.style.display = 'block';

		api( 'generate', { method: 'POST', body: form } )
			.then( function ( data ) {
				notice( ( t.done || 'Deck generated.' ) + ' ' + data.slide_count + ' slides.' );

				app.querySelector( '#noodu-project-name' ).value = '';
				app.querySelector( '#noodu-prompt' ).value = '';
				if ( reference ) {
					reference.value = '';
				}

				loadProjects();
			} )
			.catch( function ( error ) {
				notice( error.message, 'error' );
			} )
			.finally( function () {
				button.disabled = false;
				button.textContent = t.generate || 'Generate slides';
				loading.style.display = 'none';
			} );
	}

	function card( project ) {
		var el = document.createElement( 'div' );
		el.className = 'noodu-project';

		var title = document.createElement( 'div' );
		title.className = 'noodu-project__name';
		title.textContent = project.name;

		var meta = document.createElement( 'div' );
		meta.className = 'noodu-project__meta';
		meta.textContent = project.slide_count + ' slides · ' + project.model;

		var actions = document.createElement( 'div' );
		actions.className = 'noodu-project__actions';

		if ( project.download_url ) {
			var open = document.createElement( 'a' );
			open.href = project.download_url;
			open.target = '_blank';
			open.rel = 'noopener';
			open.textContent = 'Open';
			actions.appendChild( open );
		}

		var remove = document.createElement( 'button' );
		remove.type = 'button';
		remove.textContent = 'Delete';
		remove.addEventListener( 'click', function () {
			if ( ! window.confirm( t.confirmDelete || 'Delete this project?' ) ) {
				return;
			}
			remove.disabled = true;
			api( 'projects/' + project.id, { method: 'DELETE' } )
				.then( loadProjects )
				.catch( function ( error ) {
					remove.disabled = false;
					notice( error.message, 'error' );
				} );
		} );
		actions.appendChild( remove );

		el.appendChild( title );
		el.appendChild( meta );
		el.appendChild( actions );

		return el;
	}

	function loadProjects() {
		api( 'projects' )
			.then( function ( data ) {
				var section = app.querySelector( '.noodu-projects' );
				var grid = app.querySelector( '.noodu-projects__grid' );

				grid.innerHTML = '';

				if ( ! data.projects.length ) {
					section.style.display = 'none';
					return;
				}

				data.projects.forEach( function ( project ) {
					grid.appendChild( card( project ) );
				} );

				section.style.display = 'block';
			} )
			.catch( function () {
				// A failed listing should not break the generator form.
			} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		app = document.querySelector( '.noodu-app' );
		if ( ! app ) {
			return;
		}

		app.querySelector( '#noodu-fetch-models' ).addEventListener( 'click', function () {
			fetchModels( this );
		} );

		app.querySelector( '#noodu-generate' ).addEventListener( 'click', function () {
			generate( this );
		} );

		loadProjects();
	} );
}() );
