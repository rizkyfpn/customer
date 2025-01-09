<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@1.10.1"></script>
</head>

<body>
    <h1>Scan Barcode</h1>
    <video id="barcode-video" width="400" height="300"></video>
    <p id="result"></p>
    <script>
        const codeReader = new ZXing.BrowserMultiFormatReader();
        const video = document.getElementById('barcode-video');
        const result = document.getElementById('result');

        // Mulai scanning barcode
        codeReader.decodeFromVideoDevice(null, video, (resultObj, err) => {
            if (resultObj) {
                const data = resultObj.text; // Ambil data dari barcode
                result.textContent = `Barcode Data: ${data}`;

                // Kirim data ke server menggunakan Fetch API
                fetch('save.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            barcode: data
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Data berhasil disimpan!');
                        } else {
                            console.error('Server Error:', data.message);
                            alert('Gagal menyimpan data: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        alert('Terjadi kesalahan: ' + error.message);
                    });
            } else if (err) {
                console.error('Scan Error:', err);
            }
        });
    </script>
</body>

</html>