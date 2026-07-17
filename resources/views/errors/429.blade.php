<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Request — Portal Madrasah</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background:#f0fdf4; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:1rem; }
        .card { background:white; border-radius:1.5rem; padding:2.5rem; max-width:420px; width:100%; text-align:center; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
        .icon { width:64px; height:64px; background:#fef3c7; border-radius:1rem; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem; font-size:2rem; }
        h1 { font-size:1.25rem; font-weight:800; color:#1f2937; margin-bottom:.5rem; }
        p { font-size:.875rem; color:#6b7280; line-height:1.6; margin-bottom:1.5rem; }
        .badge { display:inline-block; background:#fef3c7; color:#92400e; font-size:.75rem; font-weight:700; padding:.4rem 1rem; border-radius:99px; margin-bottom:1.25rem; }
        a { display:inline-block; background:#15803d; color:white; font-size:.875rem; font-weight:600; padding:.65rem 1.5rem; border-radius:.75rem; text-decoration:none; transition:opacity .15s; }
        a:hover { opacity:.9; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏱️</div>
        <div class="badge">Terlalu Banyak Permintaan</div>
        <h1>Mohon Tunggu Sebentar</h1>
        <p>
            Anda telah melakukan terlalu banyak permintaan dalam waktu singkat.
            Silakan tunggu beberapa saat sebelum mencoba kembali.
        </p>
        <a href="javascript:history.back()">← Kembali</a>
    </div>
</body>
</html>
