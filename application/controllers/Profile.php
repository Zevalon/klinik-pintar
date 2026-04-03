<?php
class Profile extends Controller {
    protected $users;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->users = new UserModel();
        $this->users->ensureProfileSchema();
    }

    public function index() {
        $user = $this->users->findByIdWithRole(current_user()['id']);
        $this->render('profile/index', [
            'user' => $user,
        ]);
    }

    public function update() {
        verify_csrf();
        $user = $this->users->findByIdWithRole(current_user()['id']);
        if (!$user) {
            $this->respondError('User tidak ditemukan.', 404);
            redirect_to('profile');
        }

        $name = trim((string)$this->input('name'));
        $username = trim((string)$this->input('username'));
        $email = trim((string)$this->input('email'));
        $phone = normalize_phone($this->input('phone'));
        $gender = trim((string)$this->input('gender'));
        $address = trim((string)$this->input('address'));
        $bio = trim((string)$this->input('bio'));

        if ($name === '' || $username === '') {
            $this->respondError('Nama lengkap dan username wajib diisi.');
            redirect_to('profile');
        }

        if (!preg_match('/^[A-Za-z0-9._-]{3,100}$/', $username)) {
            $this->respondError('Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus, minimal 3 karakter.');
            redirect_to('profile');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respondError('Format email tidak valid.');
            redirect_to('profile');
        }

        if ($this->users->isUsernameTaken($username, $user['id'])) {
            $this->respondError('Username sudah digunakan oleh user lain.');
            redirect_to('profile');
        }

        if ($this->users->isEmailTaken($email, $user['id'])) {
            $this->respondError('Email sudah digunakan oleh user lain.');
            redirect_to('profile');
        }

        if ($gender !== '' && !in_array($gender, ['L', 'P'], true)) {
            $gender = null;
        }

        $data = [
            'name' => $name,
            'username' => $username,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
            'gender' => $gender,
            'address' => $address !== '' ? $address : null,
            'bio' => $bio !== '' ? $bio : null,
            'updated_at' => now(),
        ];

        try {
            $newPhotoPath = $this->handlePhotoUpload($user);
            if ($newPhotoPath !== null) {
                $data['photo_path'] = $newPhotoPath;
            }
        } catch (RuntimeException $e) {
            $this->respondError($e->getMessage());
            redirect_to('profile');
        }

        $this->users->updateProfile($user['id'], $data);
        $this->syncSessionUser($user['id']);
        log_activity('profile_update', 'Memperbarui profil pribadi', 'users', (int)$user['id']);

        $this->respondSuccess('Profil berhasil diperbarui.');
        redirect_to('profile');
    }

    public function updatePassword() {
        verify_csrf();
        $user = $this->users->findByIdWithRole(current_user()['id']);
        if (!$user) {
            $this->respondError('User tidak ditemukan.', 404);
            redirect_to('profile');
        }

        $currentPassword = (string)$this->input('current_password');
        $newPassword = (string)$this->input('new_password');
        $confirmPassword = (string)$this->input('new_password_confirmation');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $this->respondError('Semua field password wajib diisi.');
            redirect_to('profile');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            $this->respondError('Password saat ini tidak sesuai.');
            redirect_to('profile');
        }

        if (strlen($newPassword) < 6) {
            $this->respondError('Password baru minimal 6 karakter.');
            redirect_to('profile');
        }

        if ($newPassword !== $confirmPassword) {
            $this->respondError('Konfirmasi password baru tidak sama.');
            redirect_to('profile');
        }

        $this->users->updatePassword($user['id'], password_hash($newPassword, PASSWORD_DEFAULT));
        $this->syncSessionUser($user['id']);
        log_activity('profile_password_update', 'Mengganti password pribadi', 'users', (int)$user['id']);

        $this->respondSuccess('Password berhasil diperbarui.');
        redirect_to('profile');
    }

    public function removePhoto() {
        verify_csrf();
        $user = $this->users->findByIdWithRole(current_user()['id']);
        if (!$user) {
            $this->respondError('User tidak ditemukan.', 404);
            redirect_to('profile');
        }

        if (!empty($user['photo_path'])) {
            $this->deleteProfileFile($user['photo_path']);
        }

        $this->users->updateProfile($user['id'], [
            'photo_path' => null,
            'updated_at' => now(),
        ]);
        $this->syncSessionUser($user['id']);
        log_activity('profile_photo_remove', 'Menghapus foto profil', 'users', (int)$user['id']);

        $this->respondSuccess('Foto profil berhasil dihapus.');
        redirect_to('profile');
    }

    protected function syncSessionUser($userId) {
        $fresh = $this->users->findByIdWithRole((int)$userId);
        if (!$fresh) {
            return;
        }
        $_SESSION['user'] = $fresh;
        if (!empty($fresh['branch_id'])) {
            $_SESSION['active_branch_id'] = $fresh['branch_id'];
            $_SESSION['active_branch_name'] = $fresh['branch_name'] ?: current_branch_name();
        }
    }

    protected function handlePhotoUpload(array $user) {
        if (empty($_FILES['photo']) || empty($_FILES['photo']['name'])) {
            return null;
        }

        $file = $_FILES['photo'];
        if (!empty($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload foto profil gagal. Silakan coba lagi.');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new RuntimeException('Ukuran foto profil maksimal 2 MB.');
        }

        $tmp = $file['tmp_name'] ?? null;
        if (!$tmp || !is_uploaded_file($tmp)) {
            throw new RuntimeException('File foto profil tidak valid.');
        }

        $imageInfo = @getimagesize($tmp);
        if (!$imageInfo || empty($imageInfo['mime'])) {
            throw new RuntimeException('File yang diunggah harus berupa gambar.');
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $mime = $imageInfo['mime'];
        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Format foto profil harus JPG, PNG, atau WEBP.');
        }

        $dir = STORAGE_PATH . '/uploads/profiles';
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Folder upload profil tidak dapat dibuat.');
        }

        $filename = 'user-' . (int)$user['id'] . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $fullPath = $dir . '/' . $filename;
        if (!move_uploaded_file($tmp, $fullPath)) {
            throw new RuntimeException('Foto profil gagal disimpan.');
        }

        if (!empty($user['photo_path'])) {
            $this->deleteProfileFile($user['photo_path']);
        }

        return 'storage/uploads/profiles/' . $filename;
    }

    protected function deleteProfileFile($relativePath) {
        $relativePath = trim((string)$relativePath);
        if ($relativePath === '') {
            return;
        }

        $fullPath = BASE_PATH . '/' . ltrim($relativePath, '/');
        if (strpos(realpath(dirname($fullPath)) ?: '', realpath(STORAGE_PATH)) !== 0) {
            return;
        }
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
