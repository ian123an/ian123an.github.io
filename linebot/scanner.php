<html>
  <head>
  <meta charset="utf-8">
  <title>jsQR Demo</title>
  <script src="./jsQR.js"></script>
  <?php 
    
  system('php linebotpush.php');?>
  <!-- <link href="https://fonts.googleapis.com/css?family=Ropa+Sans" rel="stylesheet"> -->
  <style>
    body {
      font-family: 'Ropa Sans', sans-serif;
      color: #333;
      max-width: 640px;
      margin: 0 auto;
      position: relative;
    }

    #githubLink {
      position: absolute;
      right: 0;
      top: 12px;
      color: #2D99FF;
    }

    h1 {
      margin: 10px 0;
      font-size: 40px;
    }

    #loadingMessage {
      text-align: center;
      padding: 40px;
      background-color: #eee;
    }

    #canvas {
      width: 100%;
    }

    #output {
      margin-top: 20px;
      background: #eee;
      padding: 10px;
      padding-bottom: 0;
    }

    #output div {
      padding-bottom: 10px;
      word-wrap: break-word;
    }

    #noQRFound {
      text-align: center;
    }
  </style>
</head>
<body>
<form id="qrpost" action="qrdata.php" method="post">
  <h1>QR Scanner</h1>
  <!-- <a id="githubLink" href="https://github.com/cozmo/jsQR">View documentation on Github</a> -->
  <!-- <p>Pure JavaScript QR code decoding library.</p> -->
  <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
  <canvas id="canvas" hidden=""></canvas>
  <div id="output" hidden="">
    <div id="outputMessage">No QR code detected.</div>
    <div hidden=""><b>Data:</b> <span id="outputData"></span></div>
    <input type="hidden" id="qrdata" name="qrdata" value=''/>
    <input type="hidden" id="usr_id" name="usr_id" value=''/>
  </div>
  <script>
    var video = document.createElement("video");
    var canvasElement = document.getElementById("canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    var outputData = document.getElementById("outputData");
    var qrdata = document.getElementById("qrdata");
    var usr_id = document.getElementById("usr_id");

    var strUrl = location.search;
    if (strUrl.indexOf("?") != -1) {
      var getSearch = strUrl.split("?");
      var getVar = getSearch[1].split("=");
      usr_id.value=getVar[1];
    }else{
      $usr_id.value=$_POST['usr_id'];
    }

    function drawLine(begin, end, color) {
      canvas.beginPath();
      canvas.moveTo(begin.x, begin.y);
      canvas.lineTo(end.x, end.y);
      canvas.lineWidth = 4;
      canvas.strokeStyle = color;
      canvas.stroke();
    }

    // Use facingMode: environment to attemt to get the front camera on phones
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
      video.play();
      requestAnimationFrame(tick);
    });

    function tick() {
      loadingMessage.innerText = "⌛ Loading video..."
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loadingMessage.hidden = true;
        canvasElement.hidden = false;
        outputContainer.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });
        
        if (code) {
          
          drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
          drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
          drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
          drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
          outputMessage.hidden = true;
          outputData.parentElement.hidden = false;
          outputData.innerText = code.data;
          qrdata.value=code.data;
          // if(qrdata.value.indexOf(',')!=0){
          //   qrpost.submit();
          // }
        } else {
          outputMessage.hidden = false;
          outputData.parentElement.hidden = true;
        }
        
      }
        requestAnimationFrame(tick);
    }
  </script>

</form>  
<input type ="submit" value="確認送出">
</body>
</html>
