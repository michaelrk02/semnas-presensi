<div class="container">
    <div class="my-4">
        <?php echo $status; ?>
        <div class="mb-4">
            <div><b>Nama:</b> <?php echo $user['name']; ?></div>
            <div><b>E-mail:</b> <?php echo $user['email']; ?></div>
            <div><b>Nomor Telepon:</b> <?php echo $user['phone']; ?></div>
            <div><b>Asal Institusi:</b> <?php echo $user['institution']; ?></div>
            <div><a class="text-danger" onclick="return confirm('Apakah anda yakin?')" href="<?php echo site_url('presence/logout'); ?>">Logout</a></div>
        </div>
        <div><b>Silakan pilih sesi di bawah</b></div>
        <div class="list-group">
            <?php foreach ($sessions as $s): ?>
                <a href="<?php echo site_url('presence/fill').'?session_id='.urlencode($s['session_id']); ?>" class="list-group-item list-group-item-action <?php echo isset($session) && $session['session_id'] === $s['session_id'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($s['name']); ?></a>
            <?php endforeach; ?>
        </div>
        <?php if (isset($presence)): ?>
            <div class="alert alert-warning my-2">
                <h5>Informasi</h5>
                <hr>
                <div>
                    <?php echo $session['announcements']; ?>
                </div>
            </div>
            <?php if ($session_is_scheduled): ?>
                <div class="alert alert-info my-2">
                    Pengisian formulir ditutup pada <b class="timestamp"><?php echo $session['time_close']; ?></b> 
                </div>
            <?php endif; ?>
            <?php if (!$session_is_open): ?>
                <div class="alert alert-danger my-2">Pengisian formulir sudah <b>ditutup</b>! Terima kasih atas partisipasinya</div>
            <?php endif; ?>
            <div class="my-4">
                <form method="post" onsubmit="return confirm('Apakah anda yakin?')" action="<?php echo site_url('presence/fill').'?session_id='.urlencode($session['session_id']); ?>">
                    <div class="mb-4">
                        <label class="form-label">Kesan dan pesan</label>
                        <textarea <?php echo !$session_is_open ? 'readonly' : ''; ?> name="imp_msg" class="form-control" rows="5" style="resize: vertical" placeholder="Tinggalkan kesan dan pesan (opsional)"><?php echo htmlspecialchars($presence['imp_msg']); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Saran ke depan</label>
                        <textarea <?php echo !$session_is_open ? 'readonly' : ''; ?> name="suggestions" class="form-control" rows="5" style="resize: vertical" placeholder="Tinggalkan saran untuk acara ini apabila tahun depan kembali diadakan (opsional)"><?php echo htmlspecialchars($presence['suggestions']); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Usulan pembicara</label>
                        <textarea <?php echo !$session_is_open ? 'readonly' : ''; ?> name="speaker_req" class="form-control" rows="5" style="resize: vertical" placeholder="Tulis 2 pembicara yang ingin dilihat untuk tahun depan (opsional)"><?php echo htmlspecialchars($presence['speaker_req']); ?></textarea>
                    </div>
                    <div class="mb-4"><button <?php echo !$session_is_open ? 'disabled' : ''; ?> type="submit" class="btn btn-success" name="submit" value="1">Simpan</button></div>
                </form>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center" style="padding-top: 3rem; padding-bottom: 3rem">
                    <?php if (isset($session)): ?>
                        <?php if ($session_is_scheduled): ?>
                            <div class="alert alert-info">
                                Presensi untuk sesi ini dibuka pada <b class="timestamp"><?php echo $session['time_open']; ?></b> dan ditutup pada <b class="timestamp"><?php echo $session['time_close']; ?></b> 
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($session_is_open)): ?>
                            <h3>Presensi</h3>
                            <p>Silakan untuk mengisi presensi dengan mengklik tombol di bawah</p>
                            <p><a class="btn btn-success" href="<?php echo site_url('presence/fill').'?session_id='.urlencode($session['session_id']); ?>&create=1">Lakukan Presensi</a></p>
                        <?php else: ?>
                            <h3>Maaf</h3>
                            <span>Saat ini bukan merupakan waktu untuk mengisi presensi untuk sesi tersebut</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span>Silakan pilih sesi untuk presensi terlebih dahulu</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.timestamp').text(function(index, timestamp) {
        return (new Date(parseInt(timestamp) * 1000)).toString();
    });
});
</script>
