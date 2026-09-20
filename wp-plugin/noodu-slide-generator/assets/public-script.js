/* Noodu Public Scripts */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize shortcode functionality
    const wrapper = document.querySelector('.noodu-shortcode-wrapper');
    if (wrapper) {
        const fetchBtn = document.querySelector('.noodu-fetch-models');
        const generateBtn = document.querySelector('.noodu-generate-btn');

        if (fetchBtn) {
            fetchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchModels();
            });
        }

        if (generateBtn) {
            generateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                generateSlides();
            });
        }

        loadProjects();
    }
});

function fetchModels() {
    const btn = document.querySelector('.noodu-fetch-models');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Fetching models...';

    fetch(nooduPublic.restUrl + 'noodu/v1/models', {
        method: 'GET',
        headers: {
            'X-WP-Nonce': nooduPublic.nonce,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.querySelector('.noodu-model-select');
            select.innerHTML = '<option value="">Select a model...</option>';

            if (data.models && Array.isArray(data.models)) {
                data.models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.id;
                    select.appendChild(option);
                });
                alert('Found ' + data.models.length + ' models');
            }
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching models: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

function generateSlides() {
    const projectName = document.querySelector('.noodu-project-name').value;
    const model = document.querySelector('.noodu-model-select').value;
    const prompt = document.querySelector('.noodu-prompt').value;

    if (!projectName || !model || !prompt) {
        alert('Please fill all required fields');
        return;
    }

    const formData = new FormData();
    formData.append('project_name', projectName);
    formData.append('model', model);
    formData.append('prompt', prompt);

    const btn = document.querySelector('.noodu-generate-btn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Generating slides...';

    const loading = document.querySelector('.noodu-loading');
    if (loading) {
        loading.style.display = 'block';
    }

    fetch(nooduPublic.restUrl + 'noodu/v1/generate', {
        method: 'POST',
        headers: {
            'X-WP-Nonce': nooduPublic.nonce
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Slides generated successfully!');

            // Clear form
            document.querySelector('.noodu-project-name').value = '';
            document.querySelector('.noodu-prompt').value = '';
            document.querySelector('.noodu-model-select').value = '';

            // Reload projects
            loadProjects();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating slides: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (loading) {
            loading.style.display = 'none';
        }
    });
}

function loadProjects() {
    fetch(nooduPublic.restUrl + 'noodu/v1/projects', {
        method: 'GET',
        headers: {
            'X-WP-Nonce': nooduPublic.nonce,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.projects && data.projects.length > 0) {
            const grid = document.querySelector('.noodu-projects-grid');
            if (!grid) return;

            grid.innerHTML = '';

            data.projects.forEach(project => {
                const card = document.createElement('div');
                card.className = 'noodu-project-card';

                let actions = '';
                if (project.pdf_file) {
                    actions += '<a href="' + project.pdf_file + '" download>Download</a>';
                }
                actions += '<button onclick="deleteProject(' + project.id + ')">Delete</button>';

                card.innerHTML = `
                    <div class="noodu-project-name">${escapeHtml(project.name)}</div>
                    <div class="noodu-project-meta">
                        <div>Model: ${escapeHtml(project.model)}</div>
                        <div>Slides: ${project.slide_count || 'N/A'}</div>
                        <div>Status: ${escapeHtml(project.status)}</div>
                    </div>
                    <div class="noodu-project-actions">
                        ${actions}
                    </div>
                `;
                grid.appendChild(card);
            });

            document.querySelector('.noodu-projects-list').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error loading projects:', error);
    });
}

function deleteProject(projectId) {
    if (!confirm('Are you sure you want to delete this project?')) {
        return;
    }

    fetch(nooduPublic.restUrl + 'noodu/v1/projects/' + projectId, {
        method: 'DELETE',
        headers: {
            'X-WP-Nonce': nooduPublic.nonce
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Project deleted');
            loadProjects();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting project');
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
