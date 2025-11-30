// Shared admin CRUD helpers for edit/hapus actions used in admin pages
// Functions provided here match the onclick handlers used in admin pages

function safeShowModal(modalId) {
    const el = document.getElementById(modalId);
    if (!el) return;
    const m = new bootstrap.Modal(el);
    m.show();
}

// TRANSAKSI
function editTransaksi(row) {
    try {
        document.getElementById('edit_id').value = row.id || '';
        document.getElementById('edit_tanggal').value = (row.tanggal || '').slice(0, 10);
        document.getElementById('edit_deskripsi').value = row.deskripsi || '';
        document.getElementById('edit_kategori').value = row.kategori || '';
        document.getElementById('edit_nominal').value = row.nominal || '';
        safeShowModal('modalEdit');
    } catch (e) {
        console.error('editTransaksi error', e);
    }
}

function hapusTransaksi(id) {
    // Prefer an existing delete form/modal on the page; otherwise fallback to confirm + dynamic form
    const form = document.getElementById('formDeleteTransaksi');
    if (form) {
        const input = form.querySelector('input[name="id"]');
        if (input) input.value = id;
        safeShowModal('modalConfirmDelete');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) return;

    const f = document.createElement('form');
    f.method = 'POST';
    f.action = 'admin-transaksi-actions.php';

    const a = document.createElement('input');
    a.type = 'hidden';
    a.name = 'action';
    a.value = 'delete';
    f.appendChild(a);

    const i = document.createElement('input');
    i.type = 'hidden';
    i.name = 'id';
    i.value = id;
    f.appendChild(i);

    document.body.appendChild(f);
    f.submit();
}

// FASILITAS
function editFasilitas(row) {
    try {
        document.getElementById('edit_id').value = row.id || '';
        document.getElementById('edit_nama').value = row.nama || '';
        document.getElementById('edit_deskripsi').value = row.deskripsi || '';
        const current = document.getElementById('current_photo');
        if (current) {
            if (row.foto) {
                current.innerHTML = '<img src="assets/fasilitas/' + encodeURI(row.foto) + '" alt="Foto saat ini" style="max-width:150px; max-height:150px; object-fit:cover;">';
            } else {
                current.innerHTML = '';
            }
        }
        safeShowModal('modalEdit');
    } catch (e) {
        console.error('editFasilitas error', e);
    }
}

function hapusFasilitas(id) {
    const form = document.getElementById('formDeleteFasilitas');
    if (form) {
        const input = document.getElementById('delete_id');
        if (input) input.value = id;
        safeShowModal('modalConfirmDelete');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')) return;

    const f = document.createElement('form');
    f.method = 'POST';
    f.action = 'admin-fasilitas-actions.php';

    const a = document.createElement('input');
    a.type = 'hidden';
    a.name = 'action';
    a.value = 'delete';
    f.appendChild(a);

    const i = document.createElement('input');
    i.type = 'hidden';
    i.name = 'id';
    i.value = id;
    f.appendChild(i);

    document.body.appendChild(f);
    f.submit();
}

// TESTIMONI
function editTestimoni(row) {
    try {
        document.getElementById('edit_id').value = row.id || '';
        document.getElementById('edit_nama').value = row.nama || '';
        document.getElementById('edit_testimoni').value = row.testimoni || '';
        document.getElementById('edit_tanggal').value = (row.tanggal || '').slice(0, 10);
        const current = document.getElementById('current_photo');
        if (current) {
            if (row.foto) {
                current.innerHTML = '<img src="assets/testimoni/' + encodeURI(row.foto) + '" alt="Foto saat ini" style="max-width:150px; max-height:150px; object-fit:cover;">';
            } else {
                current.innerHTML = '';
            }
        }
        safeShowModal('modalEdit');
    } catch (e) {
        console.error('editTestimoni error', e);
    }
}

function hapusTestimoni(id) {
    const form = document.getElementById('formDeleteTestimoni');
    if (form) {
        const input = document.getElementById('delete_id');
        if (input) input.value = id;
        safeShowModal('modalConfirmDelete');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) return;

    const f = document.createElement('form');
    f.method = 'POST';
    f.action = 'admin-testimoni-actions.php';

    const a = document.createElement('input');
    a.type = 'hidden';
    a.name = 'action';
    a.value = 'delete';
    f.appendChild(a);

    const i = document.createElement('input');
    i.type = 'hidden';
    i.name = 'id';
    i.value = id;
    f.appendChild(i);

    document.body.appendChild(f);
    f.submit();
}

// GENERIC helpers for Paket / FAQ / other simple entities
function editGeneric(prefix, row) {
    // prefix: id prefix used in inputs, e.g., "edit_" already used in many modals
    try {
        if (row.id && document.getElementById(prefix + 'id')) document.getElementById(prefix + 'id').value = row.id;
        for (const key in row) {
            const el = document.getElementById(prefix + key);
            if (el) {
                el.value = row[key];
            }
        }
        safeShowModal('modalEdit');
    } catch (e) {
        console.error('editGeneric error', e);
    }
}

function hapusGeneric(actionUrl, data, actionFieldName = 'action', actionFieldValue = 'delete', message) {
    // data: either scalar id or object of key->value pairs
    // If there's a generic modal present, use it. Otherwise fall back to confirm + dynamic form submit.
    const modal = document.getElementById('modalDeleteGeneric');
    if (modal) {
        const form = document.getElementById('formDeleteGeneric');
        const extras = document.getElementById('formDeleteExtras');
        const msgEl = document.getElementById('modalDeleteMessage');
        // Clear extras
        extras.innerHTML = '';
        // Set form action
        form.action = actionUrl;
        // Ensure action field
        let af = form.querySelector('input[name="' + actionFieldName + '"]');
        if (!af) {
            af = document.createElement('input'); af.type = 'hidden'; af.name = actionFieldName; form.appendChild(af);
        }
        af.value = actionFieldValue;

        // Add data fields
        if (data !== null && typeof data === 'object') {
            for (const k in data) {
                const inp = document.createElement('input'); inp.type = 'hidden'; inp.name = k; inp.value = data[k]; extras.appendChild(inp);
            }
        } else {
            // assume id
            const inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'id'; inp.value = data; extras.appendChild(inp);
        }

        if (message && msgEl) msgEl.textContent = message;

        const confirmBtn = document.getElementById('modalDeleteConfirm');
        // Remove previous listeners
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        newBtn.addEventListener('click', function () { form.submit(); });

        safeShowModal('modalDeleteGeneric');
        return;
    }

    // Fallback to confirm + submit
    if (!confirm(message || 'Apakah Anda yakin ingin menghapus data ini?')) return;
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = actionUrl;

    const a = document.createElement('input');
    a.type = 'hidden';
    a.name = actionFieldName;
    a.value = actionFieldValue;
    f.appendChild(a);

    if (data !== null && typeof data === 'object') {
        for (const k in data) {
            const i = document.createElement('input'); i.type = 'hidden'; i.name = k; i.value = data[k]; f.appendChild(i);
        }
    } else {
        const i = document.createElement('input');
        i.type = 'hidden';
        i.name = 'id';
        i.value = data;
        f.appendChild(i);
    }

    document.body.appendChild(f);
    f.submit();
}

// Paket wrappers
function editPaket(row) {
    try {
        document.getElementById('edit_id').value = row.id || '';
        document.getElementById('edit_nama').value = row.nama || '';
        document.getElementById('edit_slug').value = row.slug || '';
        document.getElementById('edit_subtitle').value = row.subtitle || '';
        document.getElementById('edit_price').value = row.price || '';
        document.getElementById('edit_period').value = row.period || '';
        document.getElementById('edit_urutan').value = row.urutan || '';
        document.getElementById('edit_features').value = row.features || '';
        safeShowModal('modalEdit');
    } catch (e) {
        console.error('editPaket error', e);
    }
}

function hapusPaket(id) {
    // Paket list uses inline form with confirmation; provide fallback if called directly
    if (confirm('Hapus paket ini?')) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = 'admin-paket-actions.php';
        const a = document.createElement('input'); a.type = 'hidden'; a.name = 'action'; a.value = 'delete'; f.appendChild(a);
        const i = document.createElement('input'); i.type = 'hidden'; i.name = 'id'; i.value = id; f.appendChild(i);
        document.body.appendChild(f);
        f.submit();
    }
}

// FAQ wrappers
function editFAQ(row) {
    try {
        document.getElementById('edit_id').value = row.id || '';
        document.getElementById('edit_pertanyaan').value = row.pertanyaan || '';
        document.getElementById('edit_jawaban').value = row.jawaban || '';
        document.getElementById('edit_urutan').value = row.urutan || '';
        safeShowModal('modalEdit');
    } catch (e) {
        console.error('editFAQ error', e);
    }
}

function hapusFAQ(id) {
    // If a delete modal with id 'modalDelete' exists and has an input 'delete_id', use it
    const deleteInput = document.getElementById('delete_id');
    if (deleteInput) {
        deleteInput.value = id;
        safeShowModal('modalDelete');
        return;
    }
    // Fallback
    if (confirm('Apakah Anda yakin ingin menghapus item FAQ ini?')) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = 'admin-faq-actions.php';
        const a = document.createElement('input'); a.type = 'hidden'; a.name = 'action'; a.value = 'delete'; f.appendChild(a);
        const i = document.createElement('input'); i.type = 'hidden'; i.name = 'id'; i.value = id; f.appendChild(i);
        document.body.appendChild(f);
        f.submit();
    }
}

// Reservation approve/reject helpers
function approveReservation(id) {
    try {
        const title = document.getElementById('modalTitle');
        const msg = document.getElementById('modalMessage');
        const rid = document.getElementById('reservationId');
        const action = document.getElementById('reservationAction');
        const btn = document.getElementById('confirmButton');
        if (title) title.textContent = 'Konfirmasi Setujui Reservasi';
        if (msg) msg.textContent = 'Apakah Anda yakin ingin menyetujui reservasi ini? Jadwal akan ditandai terisi.';
        if (rid) rid.value = id;
        if (action) action.value = 'approve';
        if (btn) {
            btn.className = 'btn btn-success';
            btn.textContent = 'Setujui';
        }
        safeShowModal('modalConfirm');
    } catch (e) {
        console.error('approveReservation error', e);
    }
}

function rejectReservation(id) {
    try {
        const title = document.getElementById('modalTitle');
        const msg = document.getElementById('modalMessage');
        const rid = document.getElementById('reservationId');
        const action = document.getElementById('reservationAction');
        const btn = document.getElementById('confirmButton');
        if (title) title.textContent = 'Konfirmasi Tolak Reservasi';
        if (msg) msg.textContent = 'Apakah Anda yakin ingin menolak reservasi ini?';
        if (rid) rid.value = id;
        if (action) action.value = 'reject';
        if (btn) {
            btn.className = 'btn btn-danger';
            btn.textContent = 'Tolak';
        }
        safeShowModal('modalConfirm');
    } catch (e) {
        console.error('rejectReservation error', e);
    }
}
