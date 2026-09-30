<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

// اگر مدیر از قبل وارد شده، مستقیم به پنل برود.
if (current_admin() !== null) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $postedCsrfToken = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($postedCsrfToken)) {
        $error = 'درخواست معتبر نیست. صفحه را تازه‌سازی و دوباره تلاش کنید.';
    } elseif ($email === '' || $password === '') {
        $error = 'ایمیل و رمز عبور را وارد کنید.';
    } else {
        $pdo = db();

        $stmt = $pdo->prepare(
            'SELECT *
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
         * یکی از این ستون‌ها باید در جدول users وجود داشته باشد
         * و مقدار آن با password_hash() ذخیره شده باشد.
         */
        $passwordColumn = null;

        if (is_array($user)) {
            foreach (['password_hash', 'password', 'pass_hash'] as $column) {
                if (isset($user[$column])) {
                    $passwordColumn = $column;
                    break;
                }
            }
        }

        $passwordIsValid = (
            is_array($user)
            && $passwordColumn !== null
            && password_verify($password, (string) $user[$passwordColumn])
        );

        $accountIsActive = (
            is_array($user)
            && (int) ($user['is_active'] ?? 0) === 1
        );

        if ($passwordIsValid && $accountIsActive) {
            $roleStmt = $pdo->prepare(
                'SELECT 1
                 FROM user_roles AS ur
                 INNER JOIN roles AS r ON r.id = ur.role_id
                 WHERE ur.user_id = :user_id
                   AND r.code = :role_code
                 LIMIT 1'
            );

            $roleStmt->execute([
                'user_id'  => (int) $user['id'],
                'role_code' => 'admin',
            ]);

            if ($roleStmt->fetchColumn()) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];

                header('Location: index.php');
                exit;
            }
        }

        // عمداً پیام برای اطلاعات ورود یا دسترسی نامعتبر یکسان است.
        $error = 'ایمیل یا رمز عبور صحیح نیست، یا این حساب دسترسی مدیریت ندارد.';
    }
}

$csrfToken = csrf_token();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیر</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: grid;
            place-items: center;
            color: #172033;
            background:
                radial-gradient(circle at 15% 15%, rgba(106, 90, 249, .23), transparent 30%),
                radial-gradient(circle at 85% 85%, rgba(35, 190, 170, .17), transparent 28%),
                #f3f5fb;
            font-family: Tahoma, Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            padding: 38px;
            border: 1px solid rgba(255, 255, 255, .8);
            border-radius: 24px;
            background: rgba(255, 255, 255, .95);
            box-shadow: 0 24px 70px rgba(32, 45, 85, .13);
        }

        .brand {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            margin-bottom: 24px;
            border-radius: 18px;
            color: #fff;
            background: linear-gradient(135deg, #6558e8, #8b70f5);
            box-shadow: 0 10px 24px rgba(101, 88, 232, .3);
            font-size: 25px;
            font-weight: 700;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 25px;
        }

        .subtitle {
            margin: 0 0 28px;
            color: #778097;
            font-size: 14px;
            line-height: 1.9;
        }

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            color: #35405a;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            width: 100%;
            height: 50px;
            padding: 0 15px;
            border: 1px solid #e1e5ef;
            border-radius: 12px;
            outline: none;
            background: #fbfcff;
            color: #172033;
            font: inherit;
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus {
            border-color: #7868ed;
            box-shadow: 0 0 0 4px rgba(120, 104, 237, .12);
        }

        .error {
            margin-bottom: 18px;
            padding: 12px 14px;
            border: 1px solid #ffd8d8;
            border-radius: 11px;
            background: #fff3f3;
            color: #b42318;
            font-size: 13px;
            line-height: 1.8;
        }

        button {
            width: 100%;
            height: 52px;
            margin-top: 5px;
            border: 0;
            border-radius: 13px;
            color: #fff;
            background: linear-gradient(135deg, #6558e8, #806cf0);
            box-shadow: 0 10px 22px rgba(101, 88, 232, .24);
            cursor: pointer;
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            transition: transform .2s, box-shadow .2s;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 13px 26px rgba(101, 88, 232, .3);
        }

        .footer {
            margin: 22px 0 0;
            color: #939bad;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand" aria-hidden="true">A</div>

        <h1>ورود به پنل مدیریت</h1>
        <p class="subtitle">برای ادامه، اطلاعات حساب مدیر را وارد کنید.</p>

        <?php if ($error !== ''): ?>
            <div class="error" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken) ?>"
            >

            <div class="field">
                <label for="email">ایمیل</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= e($email) ?>"
                    placeholder="name@example.com"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="field">
                <label for="password">رمز عبور</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="رمز عبور خود را وارد کنید"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit">ورود به پنل</button>
        </form>

        <p class="footer">
            ورود فقط برای حساب‌های دارای دسترسی مدیریت مجاز است.
        </p>
    </main>
</body>
</html>