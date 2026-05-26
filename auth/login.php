<?php
session_start();

if(isset($_SESSION['login'])) {

    if($_SESSION['level'] == 'admin') {
        header('Location: ../admin/dashboard.php');
        exit;
    } else {
        header('Location: ../staff/dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <div class="text-center mb-3">
                            <img src="../assets/img/logo.png" width="80">
                        </div>
                        <h4>Login Inventory</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_GET['pesan'])) : ?>
                            <div class="alert alert-danger">
                                Username atau password salah!
                            </div>
                        <?php endif; ?>
                        <form action="proses_login.php" method="POST">
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text"
                                       name="username"
                                       class="form-control"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control"
                                           required>
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="showPassword()">
                                        Show
                                    </button>
                                </div>
                            </div>
                            <button type="submit"
                                    name="login"
                                    class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
function showPassword() {

    let pass = document.getElementById('password');

    if(pass.type === 'password') {
        pass.type = 'text';
    } else {
        pass.type = 'password';
    }
}
</script>
</body>
</html>