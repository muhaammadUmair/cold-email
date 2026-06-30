<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lead Automation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }
        .login-box {
            background: white;
            width: 380px;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .login-box h2 {
            color: #18304a;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .login-box p {
            color: #999;
            font-size: 13px;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            color: #18304a;
            margin-bottom: 6px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #1f4e7a;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #1f4e7a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #18304a;
        }
        .error-msg {
            background: #fdecea;
            color: #c0392b;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Welcome Back</h2>
        <p>Lead Automation Login</p>

        @if ($errors->any())
            <div class="error-msg">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
    <label>Username</label>
    <input type="text" name="username" value="{{ old('username') }}" required autofocus>
</div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

</body>
</html>