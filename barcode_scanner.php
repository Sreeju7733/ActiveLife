<!DOCTYPE html>
<html>
<head>
  <title>Smart Barcode Scanner</title>
  <script src="https://unpkg.com/@zxing/library@latest"></script>
</head>
<body>
  <video id="video" width="400" height="300" style="border:1px solid #ccc"></video>
  <p>Scanned Code: <span id="result"></span></p>

  <script>
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const videoElement = document.getElementById('video');
    const resultElement = document.getElementById('result');

    function isMobile() {
      return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    }

    codeReader
      .listVideoInputDevices()
      .then(videoInputDevices => {
        let selectedDeviceId;

        if (isMobile()) {
          // Prefer back camera on mobile
          selectedDeviceId = videoInputDevices.find(device => /back|rear|environment/i.test(device.label))?.deviceId;
        } else {
          // Prefer front camera on desktop
          selectedDeviceId = videoInputDevices.find(device => /front|user/i.test(device.label))?.deviceId;
        }

        // Fallback to first device if none matched
        if (!selectedDeviceId && videoInputDevices.length > 0) {
          selectedDeviceId = videoInputDevices[0].deviceId;
        }

        codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
          if (result) {
            resultElement.textContent = result.getText();
            codeReader.reset(); // Optional: stop scanning after first result
          }
        });
      })
      .catch(err => console.error(err));
  </script>
</body>
</html>

