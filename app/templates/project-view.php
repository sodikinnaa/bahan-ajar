<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';
require_once '../lib/Database.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$projectId = $_GET['id'] ?? '';
if (empty($projectId)) {
    header('Location: dashboard.php');
    exit;
}

$db = Database::getInstance(DB_PATH);
$project = $db->getProject($projectId);

if (!$project) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($project['name']) ?> - Noodu Slide Generator</title>
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
        .back-btn {
            background: #e3e6ec;
            color: #0d203f;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .back-btn:hover { background: #d9dde5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px; }

        .project-header {
            background: white;
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e3e6ec;
        }
        .project-title { font-size: 28px; font-weight: 700; margin-bottom: 16px; }
        .project-meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .meta-item { padding: 16px; background: #fafaf6; border-radius: 10px; }
        .meta-label { font-size: 12px; font-weight: 700; color: #8a93a5; text-transform: uppercase; }
        .meta-value { font-size: 18px; font-weight: 700; color: #0d203f; margin-top: 6px; }

        .section {
            background: white;
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e3e6ec;
        }
        .section h2 { font-size: 20px; font-weight: 700; margin-bottom: 20px; }

        .slides-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        .slide-item {
            background: #fafaf6;
            border: 1px solid #e3e6ec;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .slide-num { font-size: 12px; color: #8a93a5; font-weight: 600; }
        .slide-title { font-size: 14px; font-weight: 600; color: #0d203f; margin-top: 8px; }

        .revisions-list { margin-top: 20px; }
        .revision-item {
            background: #fafaf6;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
            border-left: 4px solid #3366ff;
        }
        .revision-time { font-size: 12px; color: #8a93a5; }
        .revision-prompt { font-size: 14px; color: #0d203f; margin-top: 8px; }
        .revision-status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 10px;
            background: #edf2ff;
            color: #3366ff;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
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
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e3e6ec;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
        }
        .form-group textarea:focus {
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
        .alert-info {
            background: #edf2ff;
            color: #3366ff;
        }
    </style>
</head>
<body>
<div class="header">
    <h1><?= htmlspecialchars($project['name']) ?></h1>
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<div class="container">
    <!-- Project Info -->
    <div class="project-header">
        <div class="project-title"><?= htmlspecialchars($project['name']) ?></div>
        <div class="project-meta">
            <div class="meta-item">
                <div class="meta-label">Project Code</div>
                <div class="meta-value"><?= htmlspecialchars($project['code']) ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Slides</div>
                <div class="meta-value"><?= $project['slide_count'] ?? 'N/A' ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Status</div>
                <div class="meta-value" style="color: #49c08b;"><?= htmlspecialchars($project['status']) ?></div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Created</div>
                <div class="meta-value"><?= date('M d', strtotime($project['created_at'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Slides Preview -->
    <?php if (!empty($project['slides_data']['slides'])): ?>
    <div class="section">
        <h2>Slide Preview</h2>
        <div class="slides-preview">
            <?php foreach ($project['slides_data']['slides'] as $index => $slide): ?>
            <div class="slide-item">
                <div class="slide-num">Slide <?= $index + 1 ?></div>
                <div class="slide-title"><?= htmlspecialchars(substr($slide['title'], 0, 40)) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Download Section -->
    <div class="section">
        <h2>Download</h2>
        <p style="color: #8a93a5; margin-bottom: 16px;">
            Download your generated presentation as PDF for printing or sharing.
        </p>
        <a href="../api/download.php?id=<?= htmlspecialchars($project['id']) ?>" style="
            display: inline-block;
            background: #3366ff;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        " onmouseover="this.style.background='#254dd6'" onmouseout="this.style.background='#3366ff'">
            📥 Download PDF
        </a>
    </div>

    <!-- Revisions Section -->
    <div class="section">
        <h2>Request Revision</h2>
        <p style="color: #8a93a5; margin-bottom: 20px;">
            Describe changes you'd like to make to the slides. Submit a revision request and the system will regenerate the presentation.
        </p>

        <div class="form-group">
            <label>Revision Prompt</label>
            <textarea id="revisionPrompt" placeholder="e.g., Add more examples to slide 3, change the color scheme to orange..."></textarea>
        </div>

        <div class="button-group">
            <button onclick="submitRevision('<?= htmlspecialchars($project['id']) ?>')">Submit Revision</button>
        </div>

        <?php if (!empty($project['revisions'])): ?>
        <div style="margin-top: 30px;">
            <h3 style="font-size: 16px; margin-bottom: 16px;">Revision History</h3>
            <div class="revisions-list">
                <?php foreach (array_reverse($project['revisions']) as $revision): ?>
                <div class="revision-item">
                    <div class="revision-time">
                        <?= date('M d, Y H:i', strtotime($revision['created_at'])) ?>
                    </div>
                    <div class="revision-prompt"><?= htmlspecialchars($revision['prompt']) ?></div>
                    <div class="revision-status"><?= htmlspecialchars($revision['status']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function submitRevision(projectId) {
    const prompt = document.getElementById('revisionPrompt').value;

    if (!prompt.trim()) {
        alert('Please enter a revision prompt');
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Submitting...';

    fetch('../api/projects.php?action=revise', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(projectId)}&revision_prompt=${encodeURIComponent(prompt)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Revision submitted! The system will process your changes.');
            document.getElementById('revisionPrompt').value = '';
            location.reload();
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
</script>
</body>
</html>
