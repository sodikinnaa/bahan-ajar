<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap noodu-dashboard">
    <h1><?php _e( 'Noodu Slide Generator', 'noodu-slide-generator' ); ?></h1>

    <div class="noodu-container">
        <div class="noodu-main">
            <div class="noodu-card">
                <h2><?php _e( 'Generate New Slides', 'noodu-slide-generator' ); ?></h2>

                <div class="noodu-form-group">
                    <label><?php _e( 'Project Name', 'noodu-slide-generator' ); ?></label>
                    <input type="text" id="projectName" placeholder="<?php _e( 'e.g., Module 3 - Run Your Project', 'noodu-slide-generator' ); ?>" />
                </div>

                <div class="noodu-form-group">
                    <label><?php _e( 'Model', 'noodu-slide-generator' ); ?></label>
                    <select id="modelSelect">
                        <option value=""><?php _e( 'Select a model...', 'noodu-slide-generator' ); ?></option>
                    </select>
                    <button class="button" onclick="fetchModels()"><?php _e( 'Fetch Models', 'noodu-slide-generator' ); ?></button>
                </div>

                <div class="noodu-form-group">
                    <label><?php _e( 'Prompt', 'noodu-slide-generator' ); ?></label>
                    <textarea id="promptInput" placeholder="<?php _e( 'Describe what slides you want to generate...', 'noodu-slide-generator' ); ?>" rows="6"></textarea>
                </div>

                <div class="noodu-form-group">
                    <label><?php _e( 'Reference File (Optional)', 'noodu-slide-generator' ); ?></label>
                    <input type="file" id="referenceFile" accept=".pdf,.png,.jpg,.jpeg" />
                </div>

                <button class="button button-primary button-large" onclick="generateSlides()"><?php _e( 'Generate Slides', 'noodu-slide-generator' ); ?></button>
            </div>
        </div>

        <div class="noodu-sidebar">
            <div class="noodu-card">
                <h3><?php _e( 'Quick Stats', 'noodu-slide-generator' ); ?></h3>
                <p><strong><?php _e( 'Total Projects:', 'noodu-slide-generator' ); ?></strong> <span id="totalProjects">0</span></p>
                <p><strong><?php _e( 'API Status:', 'noodu-slide-generator' ); ?></strong> <span id="apiStatus">Unconfigured</span></p>
            </div>

            <div class="noodu-card">
                <h3><?php _e( 'Documentation', 'noodu-slide-generator' ); ?></h3>
                <p><?php _e( 'Configure OpenAI API settings in', 'noodu-slide-generator' ); ?> <a href="admin.php?page=noodu-settings"><?php _e( 'Settings', 'noodu-slide-generator' ); ?></a></p>
            </div>
        </div>
    </div>
</div>

<style>
.noodu-dashboard { padding: 20px; }
.noodu-container { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
.noodu-card { background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
.noodu-form-group { margin-bottom: 15px; }
.noodu-form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
.noodu-form-group input[type="text"],
.noodu-form-group input[type="file"],
.noodu-form-group select,
.noodu-form-group textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
}
.noodu-form-group textarea { resize: vertical; }
@media (max-width: 768px) {
    .noodu-container { grid-template-columns: 1fr; }
}
</style>

<script>
function fetchModels() {
    const baseUrl = '<?php echo esc_js( get_option( 'noodu_openai_base_url' ) ); ?>';
    const apiKey = '<?php echo esc_js( get_option( 'noodu_openai_api_key' ) ); ?>';

    if ( !baseUrl || !apiKey ) {
        alert( '<?php _e( 'Please configure API settings first', 'noodu-slide-generator' ); ?>' );
        return;
    }

    fetch( '<?php echo esc_url( rest_url( 'noodu/v1/models' ) ); ?>', {
        method: 'GET',
        headers: {
            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
        }
    } )
    .then( r => r.json() )
    .then( data => {
        if ( data.success ) {
            const select = document.getElementById( 'modelSelect' );
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
    .catch( err => alert( 'Error: ' + err.message ) );
}

function generateSlides() {
    const projectName = document.getElementById( 'projectName' ).value;
    const model = document.getElementById( 'modelSelect' ).value;
    const prompt = document.getElementById( 'promptInput' ).value;

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
            window.location.href = 'admin.php?page=noodu-projects';
        } else {
            alert( 'Error: ' + data.message );
        }
    } )
    .catch( err => alert( 'Error: ' + err.message ) )
    .finally( () => {
        btn.disabled = false;
        btn.textContent = '<?php _e( 'Generate Slides', 'noodu-slide-generator' ); ?>';
    } );
}
</script>
