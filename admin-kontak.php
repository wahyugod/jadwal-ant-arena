<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

$kontak = null;
$res = $conn->query('SELECT * FROM kontak ORDER BY id ASC LIMIT 1');
if ($res) { $kontak = $res->fetch_assoc(); $res->free_result(); }
$conn->close();

$whatsapp = $kontak['whatsapp'] ?? '+62 812-3456-7890';
$email = $kontak['email'] ?? 'info@nts-arena.com';
$instagram = $kontak['instagram'] ?? 'https://instagram.com/ntsarena';

$pageTitle = 'Kelola Kontak';
$pageBreadcrumb = 'Kontak';
include 'header.php';
?>
<div class="row">
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header"><h5 class="mb-0">Informasi Kontak</h5></div>
          <div class="card-body">
            <form action="admin-kontak-actions.php" method="POST">
              <div class="mb-3">
                <label for="whatsapp" class="form-label">WhatsApp</label>
                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>" placeholder="+62 812-3456-7890" required>
                <small class="text-muted">Format: +62 xxx-xxxx-xxxx</small>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="info@nts-arena.com" required>
              </div>
              <div class="mb-3">
                <label for="instagram" class="form-label">Instagram URL</label>
                <input type="url" class="form-control" id="instagram" name="instagram" value="<?php echo htmlspecialchars($instagram); ?>" placeholder="https://instagram.com/ntsarena" required>
                <small class="text-muted">URL lengkap profil Instagram</small>
              </div>
              <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header"><h5 class="mb-0">Pratinjau</h5></div>
          <div class="card-body">
            <h6><i class="bi bi-telephone"></i> WhatsApp</h6>
            <p class="mb-3"><?php echo htmlspecialchars($whatsapp); ?></p>
            
            <h6><i class="bi bi-envelope"></i> Email</h6>
            <p class="mb-3"><?php echo htmlspecialchars($email); ?></p>
            
            <h6><i class="bi bi-instagram"></i> Instagram</h6>
            <p class="mb-0"><a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank"><?php echo htmlspecialchars($instagram); ?></a></p>
          </div>
        </div>
      </div>
    </div>
<?php include 'footer.php'; ?>

