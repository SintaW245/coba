<?php
// index.php - Halaman Utama Deteksi Foto AI
session_start();

// Konfigurasi API Sightengine
define('API_USER', '154002814'); // Ganti dengan API User Anda
define('API_SECRET', 'fa5Vs6VvTbmfh2gqiYAH5TAatb4MDvA2'); // Ganti dengan API Secret Anda

// Ambil pesan dari session jika ada
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Image Detector - Deteksi Foto AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #528562ff 0%, #0f0f0fe1 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            animation: fadeInDown 0.6s ease-out;
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .main-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease-out;
        }

        .upload-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .upload-area {
            border: 3px dashed #1d1d1fff;
            border-radius: 15px;
            padding: 60px 20px;
            background: #f8f9ff;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .upload-area:hover {
            border-color: #141314ff;
            background: #f0f2ff;
            transform: translateY(-2px);
        }

        .upload-area.dragover {
            border-color: #355f40ff;
            background: #e8ebff;
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #445c52ff;
        }

        .upload-text {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .upload-subtext {
            color: #666;
            font-size: 0.9rem;
        }

        input[type="file"] {
            display: none;
        }

        .url-input-section {
            margin: 30px 0;
            text-align: center;
        }

        .or-divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #999;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }

        .or-divider span {
            padding: 0 20px;
            font-weight: 600;
        }

        .url-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .url-input:focus {
            outline: none;
            border-color: #2b3f34ff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            background: linear-gradient(135deg, #4a6854ff 0%, #080808ff 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(20, 61, 45, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 65, 46, 0.6);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideInDown 0.5s ease-out;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .feature-card {
            text-align: center;
            padding: 20px;
            background: #f8f9ff;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .feature-desc {
            color: #666;
            font-size: 0.9rem;
        }

        .navigation {
            text-align: center;
            margin-top: 30px;
        }

        .nav-link {
            display: inline-block;
            color: #3b6649ff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #141414ff;
            transform: translateY(-2px);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }

            .main-card {
                padding: 25px;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🤖 AI Image Detector</h1>
            <p>Deteksi apakah foto Anda dibuat oleh AI atau asli</p>
        </header>

        <div class="main-card">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form id="uploadForm" action="detect.php" method="POST" enctype="multipart/form-data">
                <div class="upload-section">
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">📁</div>
                        <div class="upload-text">Klik atau drag & drop gambar di sini</div>
                        <div class="upload-subtext">Format: JPG, PNG, WEBP (Max: 5MB)</div>
                        <input type="file" id="fileInput" name="image" accept="image/jpeg,image/png,image/webp">
                    </div>
                </div>

                <div class="or-divider">
                    <span>ATAU</span>
                </div>

                <div class="url-input-section">
                    <input type="url" class="url-input" name="image_url" id="imageUrl" placeholder="Masukkan URL gambar...">
                </div>

                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p style="margin-top: 15px; color: #666;">Sedang menganalisis gambar...</p>
                </div>

                <div style="text-align: center;">
                    <button type="submit" class="btn" id="submitBtn">🔍 Deteksi Sekarang</button>
                </div>
            </form>

            <div class="features">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-title">Cepat & Akurat</div>
                    <div class="feature-desc">Hasil analisis dalam hitungan detik dengan akurasi tinggi</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-title">Aman & Private</div>
                    <div class="feature-desc">Gambar Anda tidak disimpan di server kami</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <div class="feature-title">Multi AI Detection</div>
                    <div class="feature-desc">Deteksi dari berbagai AI generator populer</div>
                </div>
            </div>

            <div class="navigation">
                <a href="history.php" class="nav-link">📊 Riwayat Deteksi</a>
                <a href="about.php" class="nav-link">ℹ️ Tentang</a>
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');
        const loading = document.getElementById('loading');
        const submitBtn = document.getElementById('submitBtn');

        // Click to upload
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // File selected
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                uploadArea.querySelector('.upload-text').textContent = `File terpilih: ${fileName}`;
                uploadArea.style.borderColor = '#28a745';
                uploadArea.style.background = '#e8f5e9';
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const fileName = files[0].name;
                uploadArea.querySelector('.upload-text').textContent = `File terpilih: ${fileName}`;
                uploadArea.style.borderColor = '#28a745';
                uploadArea.style.background = '#e8f5e9';
            }
        });

        // Form submission
        uploadForm.addEventListener('submit', (e) => {
            const hasFile = fileInput.files.length > 0;
            const hasUrl = document.getElementById('imageUrl').value.trim() !== '';

            if (!hasFile && !hasUrl) {
                e.preventDefault();
                alert('Silakan upload gambar atau masukkan URL gambar!');
                return;
            }

            loading.classList.add('active');
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>