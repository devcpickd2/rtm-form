<script>
    document.addEventListener('click', function(e) {
        let btnTambahBahan = e.target.closest('.btn-tambah-bahan');
        if(btnTambahBahan){
            let tbody = btnTambahBahan.closest('tbody.pemeriksaan');
            let index = tbody.dataset.index;

            let newRow = document.createElement('tr');
            newRow.classList.add('bahan-row');
            newRow.innerHTML = `
            <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
            <td></td> <!-- kosong, tidak ada tombol hapus bahan -->
            `;
            tbody.appendChild(newRow);
            updateRowspan(tbody);
        }

        let btnHapusPemeriksaan = e.target.closest('.btn-hapus-pemeriksaan');
        if(btnHapusPemeriksaan){
            let tbody = btnHapusPemeriksaan.closest('tbody.pemeriksaan');
            let allTbody = document.querySelectorAll('tbody.pemeriksaan');
            if(allTbody.length > 1){
                tbody.remove();
            } else {
                alert('Minimal ada 1 pemeriksaan.');
            }
        }

        function updateRowspan(tbody){
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
            .forEach(td => td.rowSpan = total);
        }
    });

// ===== TAMBAH PEMERIKSAAN =====
    document.getElementById('btnTambahPemeriksaan').addEventListener('click', function() {
        let table = document.getElementById('cookingTable');
        let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
        let clone = lastTbody.cloneNode(true);

    // hapus baris bahan tambahan, sisakan 1
        let rows = clone.querySelectorAll('tr.bahan-row');
        rows.forEach((row,i)=>{ if(i>0) row.remove(); });

    // index baru
        let index = table.querySelectorAll('tbody.pemeriksaan').length;
        clone.dataset.index = index;

    // reset value + update name
        clone.querySelectorAll('input, select, textarea').forEach(el=>{
            if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
            if(el.type === 'checkbox') el.checked = false; else el.value = '';
        });

    // reset rowspan
        clone.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
        .forEach(td=>td.rowSpan=1);

        let actionTd = clone.querySelector('.rs-action');
        actionTd.innerHTML = `
        <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
            <i class="bi bi-plus"></i>
        </button>
        `;

        lastTbody.after(clone);
    });
</script>

<!-- editttt -->
<script>
    document.addEventListener('click', function(e) {
    // ===== TAMBAH BAHAN =====
        let btnTambahBahan = e.target.closest('.btn-tambah-bahan');
        if(btnTambahBahan){
            let tbody = btnTambahBahan.closest('tbody.pemeriksaan');
            let index = tbody.dataset.index;

            let newRow = document.createElement('tr');
            newRow.classList.add('bahan-row');
            newRow.innerHTML = `
            <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
            <td></td>
            `;
            tbody.appendChild(newRow);
            updateRowspan(tbody);
        }

    // ===== HAPUS PEMERIKSAAN =====
        let btnHapusPemeriksaan = e.target.closest('.btn-hapus-pemeriksaan');
        if(btnHapusPemeriksaan){
            let tbody = btnHapusPemeriksaan.closest('tbody.pemeriksaan');
            let allTbody = document.querySelectorAll('tbody.pemeriksaan');
            if(allTbody.length > 1){
                tbody.remove();
            } else {
                alert('Minimal ada 1 pemeriksaan.');
            }
        }

    // ===== HELPER ROWSPAN =====
        function updateRowspan(tbody){
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
            .forEach(td => td.rowSpan = total);
        }
    });

// ===== TAMBAH PEMERIKSAAN =====
    document.getElementById('btnTambahPemeriksaan').addEventListener('click', function() {
        let table = document.getElementById('cookingTable');
        let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
        let clone = lastTbody.cloneNode(true);

    // hapus semua bahan tambahan, sisakan 1
        let rows = clone.querySelectorAll('tr.bahan-row');
        rows.forEach((row,i)=>{ if(i>0) row.remove(); });

    // index baru
        let index = table.querySelectorAll('tbody.pemeriksaan').length;
        clone.dataset.index = index;

    // reset value + update name
        clone.querySelectorAll('input, select, textarea').forEach(el=>{
            if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
            if(el.type === 'checkbox') el.checked = false; else el.value = '';
        });

    // reset rowspan
        clone.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
        .forEach(td=>td.rowSpan=1);

    // kolom action hanya tombol tambah bahan dan hapus pemeriksaan
        let actionTd = clone.querySelector('.rs-action');
        if(actionTd){
            actionTd.innerHTML = `
        <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
            <i class="bi bi-plus"></i>
            </button>`;
        }

        lastTbody.after(clone);
    });
</script>



<!-- create baru -->


<script>
    document.addEventListener("DOMContentLoaded", function() {

    // ===== Fungsi Helper =====
        function updateRowspan(tbody){
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
            .forEach(td => td.rowSpan = total);
        }

        document.getElementById('nama_produk').addEventListener('change', function () {
            const selected = this.selectedOptions[0];
            let spesifikasi = JSON.parse(selected.dataset.spesifikasi || '[]');

            const table = document.getElementById('cookingTable');

    // HAPUS SEMUA PEMERIKSAAN LAMA
            table.querySelectorAll('tbody.pemeriksaan').forEach(tb => tb.remove());

            spesifikasi.forEach((tahapanData, index) => {

        // 🔥 BUAT TBODY BARU
                const tbody = document.createElement('tbody');
                tbody.classList.add('pemeriksaan');
                tbody.dataset.index = index;

                const bahanArr = tahapanData.bahan || [];

                bahanArr.forEach((bahan, i) => {
                    const row = document.createElement('tr');
                    row.classList.add('bahan-row');

                    row.innerHTML = `
                        ${i === 0 ? `
            <td class="rs-pukul">
                <input type="time" name="pemasakan[${index}][pukul]" class="form-control form-control-sm">
            </td>
            <td class="rs-tahapan">
                <input type="text" name="pemasakan[${index}][tahapan]" class="form-control form-control-sm"
                value="${tahapanData.tahapan}">
                            </td>` : ''}

            <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm" value="${bahan.nama}"></td>
            <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm" value="${bahan.berat}"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>

                            ${i === 0 ? `
            <td class="rs-action">
                <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                    <i class="bi bi-plus"></i>
                </button>
            </td>

            <!-- PARAMETER -->
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][lama_proses]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_on]" value="1"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_off]" value="1"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][pressure]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="text" name="pemasakan[${index}][temperature]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][target_temp]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][actual_temp]" class="form-control form-control-sm"></td>

            <!-- PRODUK -->
            <td class="rs-produk"><input type="number" step="0.01" name="pemasakan[${index}][suhu_pusat]" class="form-control"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][warna]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][aroma]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][rasa]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][tekstur]" value="Oke"></td>

            <td class="rs-catatan"><textarea name="pemasakan[${index}][catatan]" class="form-control form-control-sm"></textarea></td>
            <td class="rs-action-pemeriksaan">
                <button type="button" class="btn btn-danger btn-sm btn-hapus-pemeriksaan">
                    <i class="bi bi-trash"></i> Hapus Pemeriksaan
                </button>
                                </td>` : ''}
                            `;

                            tbody.appendChild(row);
                        });

                updateRowspan(tbody);
                table.appendChild(tbody);
            });
});

    // ===== TAMBAH / HAPUS BAHAN / PEMERIKSAAN =====
document.addEventListener('click', function(e){
        // Tambah Bahan
    let btnTambahBahan = e.target.closest('.btn-tambah-bahan');
    if(btnTambahBahan){
        let tbody = btnTambahBahan.closest('tbody.pemeriksaan');
        let index = tbody.dataset.index;

        let newRow = document.createElement('tr');
        newRow.classList.add('bahan-row');
        newRow.innerHTML = `
                <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
                <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
                <td></td>
        `;
        tbody.appendChild(newRow);
        updateRowspan(tbody);
    }

        // Hapus Pemeriksaan
    let btnHapus = e.target.closest('.btn-hapus-pemeriksaan');
    if(btnHapus){
        let tbody = btnHapus.closest('tbody.pemeriksaan');
        let allTbody = document.querySelectorAll('tbody.pemeriksaan');
        if(allTbody.length > 1){
            tbody.remove();
        } else {
            alert('Minimal ada 1 pemeriksaan.');
        }
    }
});

    // ===== TAMBAH PEMERIKSAAN =====
document.getElementById('btnTambahPemeriksaan').addEventListener('click', function(){
    let table = document.getElementById('cookingTable');
    let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
    let clone = lastTbody.cloneNode(true);

        // Hapus bahan tambahan, sisakan 1
    let rows = clone.querySelectorAll('tr.bahan-row');
    rows.forEach((row,i)=>{ if(i>0) row.remove(); });

        // Index baru
    let index = table.querySelectorAll('tbody.pemeriksaan').length;
    clone.dataset.index = index;

        // Reset value
    clone.querySelectorAll('input, select, textarea').forEach(el=>{
        if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        if(el.type === 'checkbox') el.checked = false; else el.value = '';
    });

        // Reset rowspan
    clone.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
    .forEach(td=>td.rowSpan=1);

        // Kolom action hanya tombol tambah bahan
    let actionTd = clone.querySelector('.rs-action');
    actionTd.innerHTML = `<button type="button" class="btn btn-success btn-sm btn-tambah-bahan"><i class="bi bi-plus"></i></button>`;

    lastTbody.after(clone);
    updateRowspan(clone);
});

});
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {

    // ===== Fungsi Helper =====
        function updateRowspan(tbody){
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
            .forEach(td => td.rowSpan = total);
        }

        document.getElementById('nama_produk').addEventListener('change', function () {
            const selected = this.selectedOptions[0];
            let spesifikasi = JSON.parse(selected.dataset.spesifikasi || '[]');

// 🔥 JIKA TIDAK ADA SPESIFIKASI → BUAT 1 TAHAPAN KOSONG
            if (spesifikasi.length === 0) {
                spesifikasi = [{
                    tahapan: '',
                    bahan: []
                }];
            }


            const table = document.getElementById('cookingTable');

    // HAPUS SEMUA PEMERIKSAAN LAMA
            table.querySelectorAll('tbody.pemeriksaan').forEach(tb => tb.remove());

            spesifikasi.forEach((tahapanData, index) => {

        // 🔥 BUAT TBODY BARU
                const tbody = document.createElement('tbody');
                tbody.classList.add('pemeriksaan');
                tbody.dataset.index = index;

                let bahanArr = tahapanData.bahan || [];

                if (bahanArr.length === 0) {
                    bahanArr = [{ nama: '', berat: '' }];
                }

                bahanArr.forEach((bahan, i) => {
                    const row = document.createElement('tr');
                    row.classList.add('bahan-row');

                    row.innerHTML = `
                        ${i === 0 ? `
            <td class="rs-pukul">
                <input type="time" name="pemasakan[${index}][pukul]" class="form-control form-control-sm">
            </td>
            <td class="rs-tahapan">
                <input type="text" name="pemasakan[${index}][tahapan]" class="form-control form-control-sm"
                value="${tahapanData.tahapan}">
                            </td>` : ''}

            <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm" value="${bahan.nama}"></td>
            <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm" value="${bahan.berat}"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>

                            ${i === 0 ? `
            <td class="rs-action">
                <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                    <i class="bi bi-plus"></i>
                </button>
            </td>

            <!-- PARAMETER -->
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][lama_proses]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_on]" value="1"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_off]" value="1"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][pressure]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="text" name="pemasakan[${index}][temperature]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][target_temp]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][actual_temp]" class="form-control form-control-sm"></td>

            <!-- PRODUK -->
            <td class="rs-produk"><input type="number" step="0.01" name="pemasakan[${index}][suhu_pusat]" class="form-control"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][warna]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][aroma]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][rasa]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][tekstur]" value="Oke"></td>

            <td class="rs-catatan"><textarea name="pemasakan[${index}][catatan]" class="form-control form-control-sm"></textarea></td>
            <td class="rs-action-pemeriksaan">
                <button type="button" class="btn btn-danger btn-sm btn-hapus-pemeriksaan">
                    <i class="bi bi-trash"></i> Hapus Pemeriksaan
                </button>
                                </td>` : ''}
                            `;

                            tbody.appendChild(row);
                        });

                updateRowspan(tbody);
                table.appendChild(tbody);
            });
});


document.addEventListener('click', function(e){

    let btnTambahBahan = e.target.closest('.btn-tambah-bahan');
    if(btnTambahBahan){
        let tbody = btnTambahBahan.closest('tbody.pemeriksaan');
        let index = tbody.dataset.index;

        let newRow = document.createElement('tr');
        newRow.classList.add('bahan-row');
        newRow.innerHTML = `
                <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
                <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
                <td></td>
        `;
        tbody.appendChild(newRow);
        updateRowspan(tbody);
    }

        // Hapus Pemeriksaan
    let btnHapus = e.target.closest('.btn-hapus-pemeriksaan');
    if(btnHapus){
        let tbody = btnHapus.closest('tbody.pemeriksaan');
        let allTbody = document.querySelectorAll('tbody.pemeriksaan');
        if(allTbody.length > 1){
            tbody.remove();
        } else {
            alert('Minimal ada 1 pemeriksaan.');
        }
    }
});

    // ===== TAMBAH PEMERIKSAAN =====
document.getElementById('btnTambahPemeriksaan').addEventListener('click', function(){
    let table = document.getElementById('cookingTable');
    let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
    let clone = lastTbody.cloneNode(true);

        // Hapus bahan tambahan, sisakan 1
    let rows = clone.querySelectorAll('tr.bahan-row');
    rows.forEach((row,i)=>{ if(i>0) row.remove(); });

        // Index baru
    let index = table.querySelectorAll('tbody.pemeriksaan').length;
    clone.dataset.index = index;

        // Reset value
    clone.querySelectorAll('input, select, textarea').forEach(el=>{
        if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        if(el.type === 'checkbox') el.checked = false; else el.value = '';
    });

        // Reset rowspan
    clone.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
    .forEach(td=>td.rowSpan=1);

        // Kolom action hanya tombol tambah bahan
    let actionTd = clone.querySelector('.rs-action');
    actionTd.innerHTML = `<button type="button" class="btn btn-success btn-sm btn-tambah-bahan"><i class="bi bi-plus"></i></button>`;

    lastTbody.after(clone);
    updateRowspan(clone);
});

});
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {

    // ===== Fungsi Helper =====
        function updateRowspan(tbody){
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
            .forEach(td => td.rowSpan = total);
        }

        let allSpesifikasi = [];

        document.getElementById('nama_produk').addEventListener('change', function () {
            const selected = this.selectedOptions[0];
            let spesifikasi = JSON.parse(selected.dataset.spesifikasi || '[]');

// 🔥 JIKA TIDAK ADA SPESIFIKASI → BUAT 1 TAHAPAN KOSONG
            if (spesifikasi.length === 0) {
                spesifikasi = [{
                    tahapan: '',
                    bahan: []
                }];
            }


            const table = document.getElementById('cookingTable');

    // HAPUS SEMUA PEMERIKSAAN LAMA
            table.querySelectorAll('tbody.pemeriksaan').forEach(tb => tb.remove());

            spesifikasi.forEach((tahapanData, index) => {

        // 🔥 BUAT TBODY BARU
                const tbody = document.createElement('tbody');
                tbody.classList.add('pemeriksaan');
                tbody.dataset.index = index;

                let bahanArr = tahapanData.bahan || [];

                if (bahanArr.length === 0) {
                    bahanArr = [{ nama: '', berat: '' }];
                }

                bahanArr.forEach((bahan, i) => {
                    const row = document.createElement('tr');
                    row.classList.add('bahan-row');

                    row.innerHTML = `
                        ${i === 0 ? `
            <td class="rs-pukul">
                <input type="time" name="pemasakan[${index}][pukul]" class="form-control form-control-sm">
            </td>
            <td class="rs-tahapan">
                <input type="text" name="pemasakan[${index}][tahapan]" class="form-control form-control-sm"
                value="${tahapanData.tahapan}">
                            </td>` : ''}

            <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm" value="${bahan.nama}"></td>
            <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm" value="${bahan.berat}"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>

                            ${i === 0 ? `
            <td class="rs-action">
                <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                    <i class="bi bi-plus"></i>
                </button>
            </td>

            <!-- PARAMETER -->
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][lama_proses]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_on]" value="1"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_off]" value="1"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][pressure]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="text" name="pemasakan[${index}][temperature]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][target_temp]" class="form-control form-control-sm"></td>
            <td class="rs-parameter"><input type="number" step="0.01" name="pemasakan[${index}][actual_temp]" class="form-control form-control-sm"></td>

            <!-- PRODUK -->
            <td class="rs-produk"><input type="number" step="0.01" name="pemasakan[${index}][suhu_pusat]" class="form-control"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][warna]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][aroma]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][rasa]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][tekstur]" value="Oke"></td>

            <td class="rs-catatan"><textarea name="pemasakan[${index}][catatan]" class="form-control form-control-sm"></textarea></td>
            <td class="rs-action-pemeriksaan">
                <button type="button" class="btn btn-danger btn-sm btn-hapus-pemeriksaan">
                    <i class="bi bi-trash"></i> Hapus Pemeriksaan
                </button>
                                </td>` : ''}
                            `;

                            tbody.appendChild(row);
                        });

                updateRowspan(tbody);
                table.appendChild(tbody);
            });
});


document.addEventListener('click', function(e){

    let btnTambahBahan = e.target.closest('.btn-tambah-bahan');
    if(btnTambahBahan){
        let tbody = btnTambahBahan.closest('tbody.pemeriksaan');
        let index = tbody.dataset.index;

        let newRow = document.createElement('tr');
        newRow.classList.add('bahan-row');
        newRow.innerHTML = `
                <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
                <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
                <td></td>
        `;
        tbody.appendChild(newRow);
        updateRowspan(tbody);
    }

        // Hapus Pemeriksaan
    let btnHapus = e.target.closest('.btn-hapus-pemeriksaan');
    if(btnHapus){
        let tbody = btnHapus.closest('tbody.pemeriksaan');
        let allTbody = document.querySelectorAll('tbody.pemeriksaan');
        if(allTbody.length > 1){
            tbody.remove();
        } else {
            alert('Minimal ada 1 pemeriksaan.');
        }
    }
});

    // ===== TAMBAH PEMERIKSAAN =====
document.getElementById('btnTambahPemeriksaan').addEventListener('click', function(){
    let table = document.getElementById('cookingTable');
    let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
    let clone = lastTbody.cloneNode(true);

        // Hapus bahan tambahan, sisakan 1
    let rows = clone.querySelectorAll('tr.bahan-row');
    rows.forEach((row,i)=>{ if(i>0) row.remove(); });

        // Index baru
    let index = table.querySelectorAll('tbody.pemeriksaan').length;
    clone.dataset.index = index;

        // Reset value
    clone.querySelectorAll('input, select, textarea').forEach(el=>{
        if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        if(el.type === 'checkbox') el.checked = false; else el.value = '';
    });

        // Reset rowspan
    clone.querySelectorAll('.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action')
    .forEach(td=>td.rowSpan=1);

        // Kolom action hanya tombol tambah bahan
    let actionTd = clone.querySelector('.rs-action');
    actionTd.innerHTML = `<button type="button" class="btn btn-success btn-sm btn-tambah-bahan"><i class="bi bi-plus"></i></button>`;

    lastTbody.after(clone);
    updateRowspan(clone);
});

});
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {

    // ================= HELPER =================
        function updateRowspan(tbody) {
            let total = tbody.querySelectorAll('tr.bahan-row').length;
            tbody.querySelectorAll(
                '.rs-pukul, .rs-tahapan, .rs-parameter, .rs-produk, .rs-catatan, .rs-action'
                ).forEach(td => td.rowSpan = total);
        }

        function getSelectedSubProduk() {
            const el = document.getElementById('sub_produk');
            return el ? el.value.trim() : '';
        }

        function filterSpesifikasiBySubProduk(spesifikasi, subProduk) {
            if (!subProduk) return spesifikasi;
            return spesifikasi.filter(item =>
                !item.sub_produk || item.sub_produk === subProduk
                );
        }

        function renderSpesifikasi() {

            const produkSelect = document.getElementById('nama_produk');
            if (!produkSelect.selectedOptions.length) return;

            const selected = produkSelect.selectedOptions[0];
            const table = document.getElementById('cookingTable');

    // 🔥 RESET TOTAL (INI PENTING)
            table.querySelectorAll('tbody.pemeriksaan').forEach(tb => tb.remove());

            let spesifikasi = JSON.parse(selected.dataset.spesifikasi || '[]');
            const subProduk = getSelectedSubProduk();

            spesifikasi = filterSpesifikasiBySubProduk(spesifikasi, subProduk);

    // 🔥 JIKA KOSONG → BALIK KE 1 PEMERIKSAAN KOSONG
            if (!Array.isArray(spesifikasi) || spesifikasi.length === 0) {
                spesifikasi = [{
                    tahapan: '',
                    bahan: [{ nama: '', berat: '' }] 
                }];
            }

            spesifikasi.forEach((tahapanData, index) => {

                const tbody = document.createElement('tbody');
                tbody.classList.add('pemeriksaan');
                tbody.dataset.index = index;

                let bahanArr = Array.isArray(tahapanData.bahan) && tahapanData.bahan.length
                ? tahapanData.bahan
                : [{ nama: '', berat: '' }];

                bahanArr.forEach((bahan, i) => {

                    const row = document.createElement('tr');
                    row.classList.add('bahan-row');

                    row.innerHTML = `
                        ${i === 0 ? `
            <td class="rs-pukul"><input type="time" name="pemasakan[${index}][pukul]" class="form-control form-control-sm"></td>
                    <td class="rs-tahapan" rowspan="1">
                        <input
                        type="text"
                        name="pemasakan[${index}][tahapan]"
                        class="form-control form-control-sm"
                        value="${tahapanData.tahapan ?? ''}"
                        placeholder="Tahapan proses"
                        >
                    </td>

                            ` : ''}

            <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm" value="${bahan.nama || ''}"></td>
            <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm" value="${bahan.berat || ''}"></td>
            <td><input type="number" step="0.01" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
            <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>

                            ${i === 0 ? `
            <td class="rs-action">
                <button type="button" class="btn btn-success btn-sm btn-tambah-bahan">
                    <i class="bi bi-plus"></i>
                </button>
            </td>

            <td class="rs-parameter"><input type="number" class="form-control form-control-sm" name="pemasakan[${index}][lama_proses]"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_on]" value="1"></td>
            <td class="rs-parameter"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][paddle_off]" value="1"></td>
            <td class="rs-parameter"><input type="number" class="form-control form-control-sm" name="pemasakan[${index}][pressure]"></td>
            <td class="rs-parameter"><input type="text" class="form-control form-control-sm" name="pemasakan[${index}][temperature]"></td>
            <td class="rs-parameter"><input type="number" class="form-control form-control-sm" name="pemasakan[${index}][target_temp]"></td>
            <td class="rs-parameter"><input type="number" class="form-control form-control-sm" name="pemasakan[${index}][actual_temp]"></td>

            <td class="rs-produk"><input type="number" class="form-control" name="pemasakan[${index}][suhu_pusat]"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][warna]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][aroma]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][rasa]" value="Oke"></td>
            <td class="rs-produk"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][tekstur]" value="Oke"></td>

            <td class="rs-catatan"><textarea name="pemasakan[${index}][catatan]" class="form-control form-control-sm"></textarea></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-hapus-pemeriksaan"><i class="bi bi-trash"></i></button></td>
                                ` : ''}
                            `;
                            tbody.appendChild(row);
                        });

                updateRowspan(tbody);
                table.appendChild(tbody);
            });
}

document.getElementById('nama_produk').addEventListener('change', renderSpesifikasi);
document.getElementById('sub_produk').addEventListener('change', renderSpesifikasi);

document.addEventListener('click', function (e) {

    if (e.target.closest('.btn-tambah-bahan')) {
        let tbody = e.target.closest('tbody.pemeriksaan');
        let index = tbody.dataset.index;

        let row = document.createElement('tr');
        row.classList.add('bahan-row');
        row.innerHTML = `
                <td><input type="text" name="pemasakan[${index}][jenis_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="text" name="pemasakan[${index}][kode_bahan][]" class="form-control form-control-sm"></td>
                <td><input type="number" name="pemasakan[${index}][jumlah_standar][]" class="form-control form-control-sm"></td>
                <td><input type="number" name="pemasakan[${index}][jumlah_aktual][]" class="form-control form-control-sm"></td>
                <td class="text-center"><input type="checkbox" class="big-checkbox" name="pemasakan[${index}][sensori][]" value="Oke"></td>
                <td></td>
        `;
        tbody.appendChild(row);
        updateRowspan(tbody);
    }

    if (e.target.closest('.btn-hapus-pemeriksaan')) {
        let tbody = e.target.closest('tbody.pemeriksaan');
        if (document.querySelectorAll('tbody.pemeriksaan').length > 1) {
            tbody.remove();
        } else {
            alert('Minimal ada 1 pemeriksaan.');
        }
    }
});

document.getElementById('btnTambahPemeriksaan').addEventListener('click', function () {

    let table = document.getElementById('cookingTable');
    let lastTbody = table.querySelector('tbody.pemeriksaan:last-of-type');
    let clone = lastTbody.cloneNode(true);

    clone.querySelectorAll('tr.bahan-row').forEach((r,i)=>{ if(i>0) r.remove(); });

    let index = table.querySelectorAll('tbody.pemeriksaan').length;
    clone.dataset.index = index;

    clone.querySelectorAll('input, textarea').forEach(el => {
        if(el.name) el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
        el.type === 'checkbox' ? el.checked = false : el.value = '';
    });

    clone.querySelectorAll('.rs-pukul,.rs-tahapan,.rs-parameter,.rs-produk,.rs-catatan,.rs-action')
    .forEach(td => td.rowSpan = 1);

    lastTbody.after(clone);
    updateRowspan(clone);
});

});
</script>