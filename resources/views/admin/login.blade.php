<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* same CSS as your original (unchanged) */
* { margin:0; padding:0; box-sizing:border-box; font-family:"Segoe UI", sans-serif; }

.admin-login-body {
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#111;
}

.admin-login-card {
  max-width:380px;
  margin:auto;
  background:linear-gradient(90deg,#004302,#004D02);
  border-radius:20px;
  padding:40px 30px;
  box-shadow:0 20px 60px rgba(0,0,0,0.5);
  text-align:center;
}

.admin-login-logo img {
  width:110px;
  margin-bottom:15px;
}

.admin-login-title {
  color:#ffd700;
  margin-bottom:25px;
  font-size:24px;
  font-weight:700;
}

.admin-login-group {
  position:relative;
  margin-bottom:18px;
}

.admin-login-group i {
  position:absolute;
  top:50%;
  left:15px;
  transform:translateY(-50%);
  color:#ffd700;
}

.admin-login-group input {
  width:100%;
  padding:14px 14px 14px 45px;
  border-radius:10px;
  border:1px solid #ffd700;
  background:transparent;
  color:#fff;
  font-size:14px;
}

.admin-login-btn {
  width:100%;
  padding:14px;
  border:none;
  border-radius:12px;
  background:linear-gradient(90deg,#ffd700,#c9a400);
  color:#000;
  font-size:15px;
  font-weight:700;
  cursor:pointer;
}
</style>
</head>

<body class="admin-login-body">

<div class="admin-login-card">

    <div class="admin-login-logo">
        <img src="{{ asset('assets/image/dhanlaxmi.png') }}" alt="Admin Logo">
    </div>

    <h2 class="admin-login-title">Admin Login</h2>

    {{-- Error Message --}}
    @if(session('error'))
        <div style="color:red; margin-bottom:10px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="admin-login-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="email" placeholder="Admin Email" required>
        </div>

        <div class="admin-login-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="admin-login-btn">
            <i class="fa-solid fa-right-to-bracket"></i> Login
        </button>
    </form>

</div>

</body>
</html>
