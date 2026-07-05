<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    :root {
        --rail-blue: #1d4ed8;
        --rail-blue-dark: #1e3a8a;
        --rail-purple: #7c3aed;
        --rail-green: #198754;
    }
    body { background-color: #f1f5f9; }

    .logo-circle {
        width: 76px; height: 76px; border-radius: 50%;
        background-color: var(--rail-blue);
        display: flex; align-items: center; justify-content: center;
    }
    .logo-circle.purple { background-color: var(--rail-purple); }
    .logo-circle.green { background-color: var(--rail-green); }

    .auth-card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

    .form-control-rail { border-radius: 0.75rem; padding: 0.75rem 1rem; border: 1px solid #dbe2ea; }
    .form-control-rail:focus { border-color: var(--rail-blue); box-shadow: 0 0 0 0.2rem rgba(29,78,216,0.15); }

    .btn-rail-blue { background-color: var(--rail-blue); border-color: var(--rail-blue); color: #fff; }
    .btn-rail-blue:hover { background-color: var(--rail-blue-dark); color: #fff; }

    .btn-rail-purple { background-color: var(--rail-purple); border-color: var(--rail-purple); color: #fff; }
    .btn-rail-purple:hover { background-color: #6d28d9; color: #fff; }

    .btn-rail-green { background-color: var(--rail-green); border-color: var(--rail-green); color: #fff; }
    .btn-rail-green:hover { background-color: #157347; color: #fff; }

    .bg-purple { background-color: var(--rail-purple) !important; }

    .role-btn {
        display: flex; align-items: center; justify-content: center;
        padding: 0.6rem 1rem; border-radius: 0.75rem; border: 1px solid #dbe2ea;
        font-weight: 500; text-decoration: none; transition: all .15s ease;
    }
    .role-btn.active-passenger { background-color: var(--rail-blue); border-color: var(--rail-blue); color: #fff; }
    .role-btn.active-station { background-color: var(--rail-green); border-color: var(--rail-green); color: #fff; }
</style>
