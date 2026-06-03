<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | Smart SPMB Pro</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #db2777;
            --surface: #ffffff;
            --muted: #64748b;
            --border: #e2e8f0;
            --danger-soft: #fff1f2;
            --danger: #e11d48;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: Poppins, Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .access-denied {
            width: min(100%, 460px);
            border: 1px solid var(--border);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: 0 18px 60px rgba(15, 23, 42, 0.08);
            padding: 24px;
        }

        .access-denied__icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: var(--danger-soft);
            color: var(--danger);
            font-weight: 800;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.35rem;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .permission-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 18px 0 0;
        }

        .permission-list span {
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.78rem;
            color: #334155;
            background: #f8fafc;
        }

        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 24px;
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .primary {
            background: var(--primary);
            color: #fff;
        }

        .secondary {
            border: 1px solid var(--border);
            color: #334155;
            background: #fff;
        }

        @media (max-width: 420px) {
            .access-denied {
                padding: 20px;
                border-radius: 16px;
            }

            .actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="access-denied">
        <div class="access-denied__icon">403</div>
        <h1>Akses Ditolak</h1>
        <p><?= esc($message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.') ?></p>

        <?php if (! empty($requiredPermissions ?? [])): ?>
            <div class="permission-list" aria-label="Permission yang dibutuhkan">
                <?php foreach ($requiredPermissions as $permission): ?>
                    <span><?= esc($permission) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="<?= base_url(session('user_base_role') === 'pendaftar' ? 'pendaftar/dashboard' : (session('user_base_role') === 'operator' ? 'operator/dashboard' : 'admin/dashboard')) ?>" class="primary">Dashboard</a>
            <a href="<?= base_url('/') ?>" class="secondary">Beranda</a>
        </div>
    </main>
</body>
</html>
