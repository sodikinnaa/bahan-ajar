<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="noodu-shortcode-wrapper">
    <div class="noodu-generator-form">
        <h2><?php _e( 'Generate Educational Slides', 'noodu-slide-generator' ); ?></h2>

        <div class="noodu-form-group">
            <label><?php _e( 'Project Name', 'noodu-slide-generator' ); ?></label>
            <input type="text" class="noodu-project-name" placeholder="<?php _e( 'e.g., Module 3 - Run Your Project', 'noodu-slide-generator' ); ?>" />
        </div>

        <div class="noodu-form-group">
            <label><?php _e( 'Model', 'noodu-slide-generator' ); ?></label>
            <select class="noodu-model-select">
                <option value=""><?php _e( 'Select a model...', 'noodu-slide-generator' ); ?></option>
            </select>
            <button class="noodu-fetch-models button"><?php _e( 'Fetch Models', 'noodu-slide-generator' ); ?></button>
        </div>

        <div class="noodu-form-group">
            <label><?php _e( 'Prompt', 'noodu-slide-generator' ); ?></label>
            <textarea class="noodu-prompt" placeholder="<?php _e( 'Describe what slides you want to generate...', 'noodu-slide-generator' ); ?>" rows="6"></textarea>
        </div>

        <div class="noodu-form-group">
            <label><?php _e( 'Reference File (Optional)', 'noodu-slide-generator' ); ?></label>
            <input type="file" class="noodu-reference-file" accept=".pdf,.png,.jpg,.jpeg" />
        </div>

        <button class="noodu-generate-btn button button-primary button-large"><?php _e( 'Generate Slides', 'noodu-slide-generator' ); ?></button>
    </div>

    <div class="noodu-projects-list" style="display: none;">
        <h2><?php _e( 'Your Projects', 'noodu-slide-generator' ); ?></h2>
        <div class="noodu-projects-grid"></div>
    </div>

    <div class="noodu-loading" style="display: none; text-align: center;">
        <p><?php _e( 'Generating your slides...', 'noodu-slide-generator' ); ?></p>
        <div class="spinner" style="margin: 20px auto;"></div>
    </div>
</div>

<style>
.noodu-shortcode-wrapper {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.noodu-generator-form {
    padding: 20px;
}

.noodu-form-group {
    margin-bottom: 20px;
}

.noodu-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.noodu-form-group input[type="text"],
.noodu-form-group input[type="file"],
.noodu-form-group select,
.noodu-form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
}

.noodu-form-group textarea {
    resize: vertical;
}

.noodu-form-group input:focus,
.noodu-form-group select:focus,
.noodu-form-group textarea:focus {
    outline: none;
    border-color: #3366ff;
    box-shadow: 0 0 0 3px rgba(51, 102, 255, 0.1);
}

.noodu-fetch-models {
    margin-top: 10px;
}

.noodu-generate-btn {
    width: 100%;
    margin-top: 20px;
}

.noodu-projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.noodu-project-card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.2s;
}

.noodu-project-card:hover {
    border-color: #3366ff;
    box-shadow: 0 4px 12px rgba(51, 102, 255, 0.15);
}

.noodu-project-name {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
}

.noodu-project-meta {
    font-size: 13px;
    color: #666;
    margin-bottom: 12px;
}

.noodu-project-actions {
    display: flex;
    gap: 8px;
}

.noodu-project-actions a,
.noodu-project-actions button {
    flex: 1;
    padding: 8px;
    text-align: center;
    text-decoration: none;
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s;
}

.noodu-project-actions a:hover,
.noodu-project-actions button:hover {
    background: #3366ff;
    color: white;
    border-color: #3366ff;
}

.noodu-loading {
    padding: 40px;
    text-align: center;
}

.spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3366ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 600px) {
    .noodu-shortcode-wrapper {
        padding: 10px;
    }

    .noodu-projects-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener( 'DOMContentLoaded', function() {
    const wrapper = document.querySelector( '.noodu-shortcode-wrapper' );

    document.querySelector( '.noodu-fetch-models' ).addEventListener( 'click', function() {
        fetchModels();
    } );

    document.querySelector( '.noodu-generate-btn' ).addEventListener( 'click', function() {
        generateSlides();
    } );

    loadProjects();
} );

function fetchModels() {
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = '<?php _e( 'Fetching...', 'noodu-slide-generator' ); ?>';

    fetch( '<?php echo esc_url( rest_url( 'noodu/v1/models' ) ); ?>', {
        method: 'GET',
        headers: {
            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
        }
    } )
    .then( r => r.json() )
    .then( data => {
        if ( data.success ) {
            const select = document.querySelector( '.noodu-model-select' );
            select.innerHTML = '<option value=""><?php _e( 'Select a model...', 'noodu-slide-generator' ); ?></option>';
            data.models.forEach( model => {
                const option = document.createElement( 'option' );
                option.value = model.id;
                option.textContent = model.id;
                select.appendChild( option );
            } );
            alert( data.models.length + ' <?php _e( 'models found', 'noodu-slide-generator' ); ?>' );
        } else {
            alert( 'Error: ' + data.message );
        }
    } )
    .catch( err => alert( 'Error: ' + err.message ) )
    .finally( () => {
        btn.disabled = false;
        btn.textContent = '<?php _e( 'Fetch Models', 'noodu-slide-generator' ); ?>';
    } );
}

function generateSlides() {
    const projectName = document.querySelector( '.noodu-project-name' ).value;
    const model = document.querySelector( '.noodu-model-select' ).value;
    const prompt = document.querySelector( '.noodu-prompt' ).value;

    if ( !projectName || !model || !prompt ) {
        alert( '<?php _e( 'Please fill all required fields', 'noodu-slide-generator' ); ?>' );
        return;
    }

    const formData = new FormData();
    formData.append( 'project_name', projectName );
    formData.append( 'model', model );
    formData.append( 'prompt', prompt );

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = '<?php _e( 'Generating...', 'noodu-slide-generator' ); ?>';

    document.querySelector( '.noodu-loading' ).style.display = 'block';

    fetch( '<?php echo esc_url( rest_url( 'noodu/v1/generate' ) ); ?>', {
        method: 'POST',
        headers: {
            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
        },
        body: formData
    } )
    .then( r => r.json() )
    .then( data => {
        if ( data.success ) {
            alert( '<?php _e( 'Slides generated successfully!', 'noodu-slide-generator' ); ?>' );
            document.querySelector( '.noodu-generator-form' ).reset();
            loadProjects();
        } else {
            alert( 'Error: ' + data.message );
        }
    } )
    .catch( err => alert( 'Error: ' + err.message ) )
    .finally( () => {
        btn.disabled = false;
        btn.textContent = '<?php _e( 'Generate Slides', 'noodu-slide-generator' ); ?>';
        document.querySelector( '.noodu-loading' ).style.display = 'none';
    } );
}

function loadProjects() {
    fetch( '<?php echo esc_url( rest_url( 'noodu/v1/projects' ) ); ?>', {
        method: 'GET',
        headers: {
            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
        }
    } )
    .then( r => r.json() )
    .then( data => {
        if ( data.success && data.projects.length > 0 ) {
            const grid = document.querySelector( '.noodu-projects-grid' );
            grid.innerHTML = '';

            data.projects.forEach( project => {
                const card = document.createElement( 'div' );
                card.className = 'noodu-project-card';
                card.innerHTML = `
                    <div class="noodu-project-name">${ project.name }</div>
                    <div class="noodu-project-meta">
                        <div>Model: ${ project.model }</div>
                        <div>Slides: ${ project.slide_count || 'N/A' }</div>
                        <div>Status: ${ project.status }</div>
                    </div>
                    <div class="noodu-project-actions">
                        ${ project.pdf_file ? `<a href="${ project.pdf_file }" download><?php _e( 'Download', 'noodu-slide-generator' ); ?></a>` : '' }
                        <button onclick="deleteProject(${ project.id })"><?php _e( 'Delete', 'noodu-slide-generator' ); ?></button>
                    </div>
                `;
                grid.appendChild( card );
            } );

            document.querySelector( '.noodu-projects-list' ).style.display = 'block';
        }
    } );
}

function deleteProject( projectId ) {
    if ( !confirm( '<?php _e( 'Are you sure?', 'noodu-slide-generator' ); ?>' ) ) return;

    fetch( '<?php echo esc_url( rest_url( 'noodu/v1/projects/' ) ); ?>' + projectId, {
        method: 'DELETE',
        headers: {
            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
        }
    } )
    .then( r => r.json() )
    .then( data => {
        if ( data.success ) {
            loadProjects();
        } else {
            alert( 'Error: ' + data.message );
        }
    } );
}
</script>
