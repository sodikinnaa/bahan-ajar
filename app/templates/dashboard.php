<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';
require_once '../lib/Database.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance(DB_PATH);
$projects = $db->getAllProjects();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Noodu Slide Generator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fafaf6;
            color: #0d203f;
        }
        .header {
            background: white;
            border-bottom: 1px solid #e3e6ec;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 24px; font-weight: 700; }
        .logout-btn {
            background: #f2722b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }
        .logout-btn:hover { background: #d65a1f; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px; }
        .tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            border-bottom: 2px solid #e3e6ec;
        }
        .tab {
            padding: 12px 0;
            cursor: pointer;
            font-weight: 600;
            color: #8a93a5;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        .tab.active {
            color: #3366ff;
            border-bottom-color: #3366ff;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .form-section {
            background: white;
            border-radius: 14px;
            padding: 30px;
            border: 1px solid #e3e6ec;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #0d203f;
            margin-bottom: 8px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e3e6ec;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3366ff;
            box-shadow: 0 0 0 3px rgba(51, 102, 255, 0.1);
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        button {
            flex: 1;
            padding: 12px 20px;
            background: #3366ff;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #254dd6; }
        button:disabled {
            background: #d9dde5;
            cursor: not-allowed;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .project-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #e3e6ec;
            transition: all 0.2s;
        }
        .project-card:hover {
            border-color: #3366ff;
            box-shadow: 0 6px 20px rgba(51, 102, 255, 0.12);
        }
        .project-name { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .project-meta {
            font-size: 13px;
            color: #8a93a5;
            margin-bottom: 16px;
        }
        .project-actions {
            display: flex;
            gap: 8px;
        }
        .project-actions a,
        .project-actions button {
            flex: 1;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            background: #edf2ff;
            color: #3366ff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .project-actions a:hover,
        .project-actions button:hover {
            background: #3366ff;
            color: white;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: #e2f4eb;
            color: #1f9a68;
        }
        .alert-error {
            background: #fff0e5;
            color: #c2571a;
        }
        .alert-info {
            background: #edf2ff;
            color: #3366ff;
        }

        .loading { display: none; }
        .loading.active {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Noodu Slide Generator</h1>
    <form method="POST" action="../api/login.php" style="display: inline;">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

<div class="container">
    <div class="tabs">
        <div class="tab active" onclick="switchTab('generate')">Generate New</div>
        <div class="tab" onclick="switchTab('projects')">Projects</div>
    </div>

    <!-- Generate Tab -->
    <div id="generate" class="tab-content active">
        <div class="form-grid">
            <!-- API Settings -->
            <div class="form-section">
                <h2 style="margin-bottom: 20px;">OpenAI API Settings</h2>
                <div class="form-group">
                    <label>Base URL</label>
                    <input type="url" id="baseUrl" placeholder="https://api.openai.com/v1" value="https://api.openai.com/v1">
                </div>
                <div class="form-group">
                    <label>API Key</label>
                    <input type="password" id="apiKey" placeholder="sk-...">
                </div>
                <div class="form-group">
                    <label>Model</label>
                    <select id="model">
                        <option value="">Select a model...</option>
                    </select>
                    <button onclick="fetchModels()" style="margin-top: 10px; width: 100%;">Fetch Available Models</button>
                </div>
            </div>

            <!-- Project Settings -->
            <div class="form-section">
                <h2 style="margin-bottom: 20px;">Project Settings</h2>
                <div class="form-group">
                    <label>Project Name</label>
                    <input type="text" id="projectName" placeholder="e.g., Module 3 - Run Your Project">
                </div>
                <div class="form-group">
                    <label>Reference File (PDF/PNG)</label>
                    <input type="file" id="referenceFile" accept=".pdf,.png,.jpg,.jpeg">
                </div>
                <div class="form-group">
                    <label>Prompt</label>
                    <textarea id="prompt" placeholder="Describe what slides you want to generate..."></textarea>
                </div>
                <div class="button-group">
                    <button onclick="generateSlides()">Generate Slides</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Tab -->
    <div id="projects" class="tab-content">
        <h2 style="margin-bottom: 20px;">Your Projects</h2>
        <?php if (empty($projects)): ?>
        <div class="alert alert-info">No projects yet. Create your first module in the "Generate New" tab.</div>
        <?php else: ?>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
            <div class="project-card">
                <div class="project-name"><?= htmlspecialchars($project['name']) ?></div>
                <div class="project-meta">
                    <div>Created: <?= date('M d, Y', strtotime($project['created_at'])) ?></div>
                    <div>Slides: <?= $project['slide_count'] ?? 'N/A' ?></div>
                    <div>Status: <span style="color: #49c08b; font-weight: 600;"><?= htmlspecialchars($project['status']) ?></span></div>
                </div>
                <div class="project-actions">
                    <a href="project-view.php?id=<?= htmlspecialchars($project['id']) ?>">View</a>
                    <a href="../api/download.php?id=<?= htmlspecialchars($project['id']) ?>">Download</a>
                    <button onclick="deleteProject('<?= htmlspecialchars($project['id']) ?>')">Delete</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

function fetchModels() {
    const baseUrl = document.getElementById('baseUrl').value;
    const apiKey = document.getElementById('apiKey').value;

    if (!baseUrl || !apiKey) {
        alert('Please enter Base URL and API Key');
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Fetching...';

    fetch('../api/models.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `base_url=${encodeURIComponent(baseUrl)}&api_key=${encodeURIComponent(apiKey)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('model');
            select.innerHTML = '<option value="">Select a model...</option>';
            data.models.forEach(model => {
                const option = document.createElement('option');
                option.value = model.id;
                option.textContent = model.id;
                select.appendChild(option);
            });
            alert(`Found ${data.count} models`);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => alert('Error: ' + err.message))
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Fetch Available Models';
    });
}

function generateSlides() {
    const baseUrl = document.getElementById('baseUrl').value;
    const apiKey = document.getElementById('apiKey').value;
    const model = document.getElementById('model').value;
    const projectName = document.getElementById('projectName').value;
    const prompt = document.getElementById('prompt').value;
    const referenceFile = document.getElementById('referenceFile').files[0];

    if (!baseUrl || !apiKey || !model || !projectName || !prompt) {
        alert('Please fill all required fields');
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Generating...';

    const formData = new FormData();
    formData.append('base_url', baseUrl);
    formData.append('api_key', apiKey);
    formData.append('model', model);
    formData.append('project_name', projectName);
    formData.append('prompt', prompt);
    if (referenceFile) {
        formData.append('reference_file', referenceFile);
    }

    fetch('../api/generate.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Slides generated successfully!');
            document.getElementById('projectName').value = '';
            document.getElementById('prompt').value = '';
            document.getElementById('referenceFile').value = '';
            document.querySelector('.tab').click(); // Switch to projects tab
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => alert('Error: ' + err.message))
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

function deleteProject(projectId) {
    if (!confirm('Are you sure you want to delete this project?')) return;

    fetch('../api/projects.php?action=delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(projectId)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Project deleted');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => alert('Error: ' + err.message));
}
</script>
</body>
</html>
