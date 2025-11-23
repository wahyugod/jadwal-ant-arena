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
    </body>

    </html>