:root {
    --bg: #0f172a;
    --bg-soft: #111827;
    --panel: #111827;
    --panel-alt: #1f2937;
    --primary: #7c3aed;
    --primary-soft: #8b5cf6;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --text: #e5e7eb;
    --muted: #94a3b8;
    --line: rgba(148, 163, 184, 0.2);
    --shadow: rgba(15, 23, 42, 0.35);
}

body.light {
    --bg: #f3f4f6;
    --bg-soft: #ffffff;
    --panel: #ffffff;
    --panel-alt: #eef2ff;
    --primary: #6d28d9;
    --primary-soft: #7c3aed;
    --success: #059669;
    --warning: #d97706;
    --danger: #dc2626;
    --text: #111827;
    --muted: #475569;
    --line: rgba(15, 23, 42, 0.08);
    --shadow: rgba(37, 99, 235, 0.08);
}

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--bg) 0%, #111827 100%);
    color: var(--text);
}

body.light {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

img {
    max-width: 100%;
}

a {
    text-decoration: none;
    color: inherit;
}

button, input, select, textarea {
    font: inherit;
}

.login-body {
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 24px;
}

.login-wrapper {
    width: min(100%, 460px);
}

.login-card {
    background: rgba(17, 24, 39, 0.8);
    border: 1px solid var(--line);
    border-radius: 22px;
    padding: 28px 24px;
    backdrop-filter: blur(10px);
    box-shadow: 0 24px 48px var(--shadow);
}

body.light .login-card {
    background: rgba(255, 255, 255, 0.8);
}

.login-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 8px;
}

.brand-mark {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-soft) 100%);
    color: white;
    font-weight: 800;
}

.login-brand h1 {
    margin: 0;
    font-size: 1.8rem;
}

.login-subtitle {
    text-align: center;
    color: var(--muted);
    margin-bottom: 20px;
}

.login-form {
    display: grid;
    gap: 16px;
}

.login-form label, .form-grid label {
    display: grid;
    gap: 8px;
    font-weight: 600;
    color: var(--text);
}

input, select, textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: rgba(15, 23, 42, 0.35);
    color: var(--text);
    outline: none;
}

body.light input,
body.light select,
body.light textarea {
    background: #f8fafc;
}

input:focus, select:focus, textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
    border-radius: 12px;
    padding: 12px 18px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.2s ease, opacity 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-soft) 100%);
    color: white;
}

.btn-secondary {
    background: rgba(148, 163, 184, 0.12);
    color: var(--text);
    border: 1px solid var(--line);
}

.btn-danger {
    background: rgba(239, 68, 68, 0.12);
    color: #fecaca;
    border: 1px solid rgba(239, 68, 68, 0.4);
}

.btn-small {
    padding: 8px 12px;
    font-size: 0.82rem;
}

.btn-full {
    width: 100%;
}

.container {
    width: min(1200px, calc(100% - 32px));
    margin: 0 auto;
    padding: 32px 0 48px;
}

.topbar {
    position: sticky;
    top: 0;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 28px;
    background: rgba(15, 23, 42, 0.7);
    border-bottom: 1px solid var(--line);
    backdrop-filter: blur(12px);
}

body.light .topbar {
    background: rgba(255, 255, 255, 0.7);
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 800;
    letter-spacing: 0.04em;
}

.main-nav {
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
    color: var(--muted);
}

.main-nav a {
    transition: color 0.2s ease;
}

.main-nav a:hover {
    color: var(--text);
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-pill {
    background: rgba(124, 58, 237, 0.15);
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 8px 12px;
    color: var(--text);
    font-size: 0.9rem;
}

.theme-toggle {
    border: 1px solid var(--line);
    background: rgba(148, 163, 184, 0.08);
    color: var(--text);
    width: 42px;
    height: 42px;
    border-radius: 12px;
    cursor: pointer;
}

.page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
}

.eyebrow {
    margin: 0 0 6px;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--muted);
    font-size: 0.75rem;
}

.page-head h1 {
    margin: 0;
    font-size: clamp(1.9rem, 4vw, 2.7rem);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.stats-grid.small {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.stat-card {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 22px 18px;
    box-shadow: 0 18px 40px var(--shadow);
}

.stat-card span {
    color: var(--muted);
    display: block;
    margin-bottom: 14px;
}

.stat-card strong {
    font-size: clamp(1.7rem, 4vw, 2.2rem);
}

.panel {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 22px 18px;
    margin-bottom: 22px;
    box-shadow: 0 18px 40px var(--shadow);
}

.panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.panel-head h2 {
    margin: 0;
    font-size: 1.3rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.full-width {
    grid-column: 1 / -1;
}

.form-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}

th, td {
    text-align: left;
    padding: 14px 12px;
    border-bottom: 1px solid var(--line);
}

th {
    color: var(--muted);
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.inline-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
}

.badge-success {
    background: rgba(16, 185, 129, 0.12);
    color: var(--success);
}

.badge-warning {
    background: rgba(245, 158, 11, 0.12);
    color: var(--warning);
}

.alert {
    padding: 12px 14px;
    border-radius: 12px;
    margin-bottom: 18px;
    font-weight: 600;
}

.alert-success {
    background: rgba(16, 185, 129, 0.12);
    color: #a7f3d0;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.alert-error {
    background: rgba(239, 68, 68, 0.12);
    color: #fecaca;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.alert-info {
    background: rgba(59, 130, 246, 0.12);
    color: #bfdbfe;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.profile-box {
    display: grid;
    gap: 8px;
    padding: 12px 0;
}

.demo-credentials {
    margin-top: 18px;
    padding-top: 18px;
    border-top: 1px solid var(--line);
    color: var(--muted);
    text-align: center;
    line-height: 1.6;
}

.mt-2 {
    margin-top: 18px;
}

@media (max-width: 900px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .topbar {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 680px) {
    .stats-grid,
    .profile-grid,
    .form-grid {
        grid-template-columns: 1fr;
    }

    .page-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .main-nav {
        justify-content: center;
    }

    .user-menu {
        width: 100%;
        justify-content: center;
    }
}
