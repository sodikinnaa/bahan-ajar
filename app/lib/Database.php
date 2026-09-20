<?php
class Database {
    private static $instance = null;
    private $data = [];
    private $dbPath;

    private function __construct($dbPath) {
        $this->dbPath = $dbPath;
        $this->load();
    }

    public static function getInstance($dbPath) {
        if (self::$instance === null) {
            self::$instance = new self($dbPath);
        }
        return self::$instance;
    }

    private function load() {
        if (file_exists($this->dbPath)) {
            $json = file_get_contents($this->dbPath);
            $this->data = json_decode($json, true) ?? ['projects' => []];
        } else {
            $this->data = ['projects' => []];
        }
    }

    public function save() {
        file_put_contents($this->dbPath, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return true;
    }

    public function addProject($project) {
        $id = bin2hex(random_bytes(8));
        $project['id'] = $id;
        $project['created_at'] = date('c');
        $project['updated_at'] = date('c');
        $project['revisions'] = [];

        $this->data['projects'][$id] = $project;
        $this->save();

        return $project;
    }

    public function updateProject($id, $updates) {
        if (!isset($this->data['projects'][$id])) {
            return false;
        }

        $this->data['projects'][$id] = array_merge($this->data['projects'][$id], $updates);
        $this->data['projects'][$id]['updated_at'] = date('c');
        $this->save();

        return $this->data['projects'][$id];
    }

    public function addRevision($projectId, $revision) {
        if (!isset($this->data['projects'][$projectId])) {
            return false;
        }

        $revisionId = bin2hex(random_bytes(8));
        $revision['id'] = $revisionId;
        $revision['created_at'] = date('c');

        $this->data['projects'][$projectId]['revisions'][] = $revision;
        $this->save();

        return $revision;
    }

    public function getProject($id) {
        return $this->data['projects'][$id] ?? null;
    }

    public function getAllProjects() {
        return array_values($this->data['projects']);
    }

    public function deleteProject($id) {
        if (isset($this->data['projects'][$id])) {
            unset($this->data['projects'][$id]);
            $this->save();
            return true;
        }
        return false;
    }
}
?>
