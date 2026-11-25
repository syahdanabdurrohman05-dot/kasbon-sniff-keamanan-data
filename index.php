<?php
require 'koneksi.php';

// Proses Simpan Data (POST -> Redirect -> GET agar tidak dobel saat di-refresh)
if (isset($_POST['simpan'])) {
    $nama = enkripsi($_POST['nama_pelanggan']);
    $detail = enkripsi($_POST['detail_kasbon']);

    $stmt = $conn->prepare("INSERT INTO catatan_kasbon (nama_pelanggan, detail_kasbon) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $detail);
    $stmt->execute();

    header("Location: index.php?status=sukses");
    exit;
}

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM catatan_kasbon WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php?status=terhapus");
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM catatan_kasbon ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi Catat Kasbon Warung</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-xl font-bold text-amber-400">📒 Sistem Informasi Catat Kasbon Warung</h1>
        
        <?php if (isset($_GET['status']) && $_GET['status'] === 'sukses'): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 p-3 rounded text-sm">
                Catatan kasbon berhasil dienkripsi dan disimpan ke MySQL!
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'terhapus'): ?>
            <div class="bg-rose-500/20 border border-rose-500 text-rose-300 p-3 rounded text-sm">
                Data kasbon berhasil dihapus dari database!
            </div>
        <?php endif; ?>

        <!-- Form Input (Enkripsi) -->
        <form method="POST" class="bg-slate-800 p-5 rounded-xl space-y-4 border border-slate-700">
            <h2 class="font-semibold text-sm text-slate-300">Catat Kasbon Baru (Dienkripsi Sebelum Masuk Database)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan (misal: Kang Asep)" class="bg-slate-950 border border-slate-700 p-2.5 rounded text-sm" required>
                <input type="text" name="detail_kasbon" placeholder="Barang & Nominal (misal: Beras 5kg - Rp 70.000)" class="bg-slate-950 border border-slate-700 p-2.5 rounded text-sm" required>
            </div>
            <button type="submit" name="simpan" class="bg-amber-600 hover:bg-amber-500 px-4 py-2 rounded text-sm font-semibold text-white">
                🔒 Enkripsi & Simpan Kasbon
            </button>
        </form>

        <!-- Tabel Output (Dekripsi) -->
        <div class="bg-slate-800 p-5 rounded-xl border border-slate-700">
            <h2 class="font-semibold text-sm text-slate-300 mb-3">Daftar Kasbon Terdekripsi</h2>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-700 text-slate-400">
                        <th class="p-2">ID</th>
                        <th class="p-2">Ciphertext di Jalur MySQL (Port 3306)</th>
                        <th class="p-2">Nama Pelanggan (Terdekripsi)</th>
                        <th class="p-2">Detail Kasbon (Terdekripsi)</th>
                        <th class="p-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td class="p-2"><?= $row['id'] ?></td>
                        <td class="p-2 font-mono text-amber-400"><?= substr($row['nama_pelanggan'], 0, 22) ?>...</td>
                        <td class="p-2 font-bold text-white"><?= htmlspecialchars(dekripsi($row['nama_pelanggan'])) ?></td>
                        <td class="p-2 text-slate-300"><?= htmlspecialchars(dekripsi($row['detail_kasbon'])) ?></td>
                        <td class="p-2 text-center">
                            <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus catatan kasbon ini?')" class="bg-rose-600 hover:bg-rose-500 text-white px-2.5 py-1 rounded text-[11px] font-medium">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>