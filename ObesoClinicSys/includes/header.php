<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - BioBridge Medical Center</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css">

  <!-- Optional: Your custom styles -->
  <link href="../includes/css/style.css" rel="stylesheet" />
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand d-flex align-items-center ps-3" href="../public/dashboard.php">
      <img src="../assets/images/obeso medical clinic logo.png" alt="BioBridge Medical Center Logo" style="height: 50px; width: 50px;" class="me-2" />
      <span class="fw-bold text-white">OBESO CLINIC</span>
    </a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link text-white" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <div class="input-group">
      </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav  me-0 me-md-3 my-2 my-md-0 ms-auto ms-md-0 me-3 me-lg-4">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
          aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

          <li>
            <hr class="dropdown-divider" />
          </li>
          <li><a class="dropdown-item" href="/Obeso-Clinic-Management-System/Public/logout.php">Logout</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</body>