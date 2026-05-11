<?php
session_start();

require_once '../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $auth = new AuthController();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $login = $auth->login($email, $password);

    if ($login) {

        if ($_SESSION["UserRole"] === "traveler") {
            header("Location: ./Traveller/travellerHome.php");
        }

        elseif ($_SESSION["UserRole"] === "guide") {
            header("Location: ./Guide/guideDashboard.php");
        }

        elseif ($_SESSION["UserRole"] === "admin") {
            header("Location: ./Admin/adminDashboard.php");
        }

        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | EcoVoyage</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet"
          href="./Assets/css/login.css">

</head>

<body>

<div class="login-container">

    <div class="overlay"></div>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="row w-100 justify-content-center">

            <div class="col-lg-5 col-md-7">

                <div class="card login-card shadow-lg">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">

                            <h2 class="fw-bold text-success">

                                <i class="fas fa-leaf"></i>
                                EcoVoyage

                            </h2>

                            <p class="text-muted">
                                Sustainable Travel Starts Here
                            </p>

                        </div>

                        <!-- ERROR MESSAGE -->
                        <?php if(isset($_SESSION["errMsg"])): ?>

                            <div class="alert alert-danger">

                                <?= $_SESSION["errMsg"] ?>

                            </div>

                            <?php unset($_SESSION["errMsg"]); ?>

                        <?php endif; ?>

                        <!-- FORM -->
                        <form id="loginForm"
                              method="POST"
                              action="login.php">

                            <!-- Email -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="fas fa-envelope"></i>

                                    </span>

                                    <input type="email"
                                           class="form-control"
                                           id="email"
                                           name="email"
                                           placeholder="Enter your email">

                                </div>

                                <small class="text-danger"
                                       id="emailError"></small>

                            </div>

                            <!-- Password -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="fas fa-lock"></i>

                                    </span>

                                    <input type="password"
                                           class="form-control"
                                           id="password"
                                           name="password"
                                           placeholder="Enter password">

                                </div>

                                <small class="text-danger"
                                       id="passwordError"></small>

                            </div>

                            <!-- Remember -->
                            <div class="mb-4">

                                <input type="checkbox"
                                       id="remember"
                                       name="remember_me">

                                <label for="remember">
                                    Remember me
                                </label>

                            </div>

                            <!-- BUTTON -->
                            <button type="submit"
                                    class="btn btn-success w-100 rounded-pill">

                                Login

                            </button>

                        </form>

                        <div class="text-center mt-4">

                            <p class="small">

                                Don't have an account?

                                <a href="register.php"
                                   class="text-success fw-bold">

                                    Register

                                </a>

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="./Assets/js/login.js"></script>

</body>
</html>