    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});

// Dropdown toggle untuk Pengaturan Web
document.querySelectorAll('.nav-dropdown-toggle').forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        const dropdown = this.closest('.nav-dropdown');
        dropdown.classList.toggle('show');
    });
});
    </script>
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
    <script src="assets/js/admin-crud.js"></script>
    <!-- Generic Delete Confirmation Modal (used by multiple admin pages) -->
    <div class="modal fade" id="modalDeleteGeneric" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modalDeleteMessage">Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <form id="formDeleteGeneric" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <div id="formDeleteExtras"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="modalDeleteConfirm">Hapus</button>
                </div>
            </div>
        </div>
    </div>
    </body>

    </html>