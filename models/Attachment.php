<?php
/**
 * Attachment Model
 */

class Attachment {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get attachments for an activity
     */
    public function getByActivity($activityId) {
        $stmt = $this->db->prepare("SELECT * FROM attachments WHERE activity_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$activityId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get attachment by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM attachments WHERE attachment_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create attachment
     */
    public function create($data) {
        $sql = "INSERT INTO attachments (activity_id, file_name, original_name, file_type, file_size, file_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['activity_id'],
            $data['file_name'],
            $data['original_name'],
            $data['file_type'],
            $data['file_size'],
            $data['file_path']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Delete attachment
     */
    public function delete($id) {
        // Get file path first
        $attachment = $this->getById($id);
        
        if ($attachment) {
            // Delete file from disk
            $fullPath = UPLOAD_DIR . $attachment['file_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            // Delete from database
            $stmt = $this->db->prepare("DELETE FROM attachments WHERE attachment_id = ?");
            return $stmt->execute([$id]);
        }
        
        return false;
    }
    
    /**
     * Validate uploaded file
     */
    public function validateFile($file) {
        $errors = [];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Filopplasting feilet med kode: ' . $file['error'];
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Filen er for stor. Maks ' . formatFileSize(MAX_FILE_SIZE) . ' tillatt.';
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            $errors[] = 'Filtype ikke tillatt. Tillatte typer: ' . implode(', ', ALLOWED_EXTENSIONS);
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
            $errors[] = 'Ugyldig filtype.';
        }
        
        return $errors;
    }
    
    /**
     * Upload file
     */
    public function uploadFile($file, $activityId) {
        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $filePath = date('Y/m') . '/' . $fileName;
        $fullPath = UPLOAD_DIR . $filePath;
        
        // Create directory if not exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception('Kunne ikke lagre filen');
        }
        
        // Save to database
        return $this->create([
            'activity_id' => $activityId,
            'file_name' => $fileName,
            'original_name' => $file['name'],
            'file_type' => $extension,
            'file_size' => $file['size'],
            'file_path' => $filePath
        ]);
    }
}
