<!DOCTYPE html>
<html>
<head>
    <title>Menyiapkan PDF...</title>
    <script>
        window.onload = function() {
            // Ambil token dari localStorage
            const token = localStorage.getItem('token');
            if (!token) {
                document.getElementById('error').style.display = 'block';
                return;
            }
            
            // Redirect ke API dengan token
            window.location.href = "{{ $apiUrl }}?token=" + token;
        };
    </script>
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <h2>Menyiapkan dokumen PDF...</h2>
        <p>Mohon tunggu sebentar.</p>
        
        <div id="error" style="display: none; color: red; margin-top: 20px;">
            <p>Tidak dapat menemukan informasi login Anda. Silakan login kembali.</p>
            <a href="/login">Login</a>
        </div>
    </div>
</body>
</html>