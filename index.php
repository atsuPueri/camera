<?php

if (isset($_POST["img_data"])) {
    dataURL_decode("./test.png", $_POST["img_data"]);
}

function dataURL_decode(string $file_path_name, string $dataURL): void
{
    // dataURLか確認 // 確認機能未実装

    // base64の値を取得
    $base64 = explode(";base64,", $dataURL)[1];
    // base64をエンコード
    $decode = base64_decode($base64);

    // 書き出し
    file_put_contents($file_path_name, $decode);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        video{
            width: 375px;
            height: 375px;
        }
        canvas {
            border: solid black 1px;
        }
    </style>
</head>

<body>
    <?php // playsinlineが絶対いる ?>
    <video id="camera" playsinline></video>
    <button id="shutter">撮影</button>

    <script>
        // 読み込まれたら
        window.addEventListener("load", () => {

            // カメラ描写要素
            const video = document.getElementById("camera");

            // モバイル端末の背面カメラで動作
            navigator.mediaDevices
                .getUserMedia({
                    audio: false,
                    video: {
                        facingMode: {
                            exact: 'environment',
                        },
                    },
                })
                .then((stream) => {
                    video.srcObject = stream
                    video.onloadedmetadata = () => {
                        video.play()
                    }
                })
                .catch(() => {
                    alert("Error");
                })

            // シャッターボタンが押されたとき
            document.getElementById("shutter").addEventListener("click", () => {

                // 一時貼り付け場所
                const canvas = document.createElement("canvas");
                canvas.width = video.videoHeight;
                canvas.height = video.videoWidth;

                document.querySelector("body").appendChild(canvas);

                const ctx = canvas.getContext("2d");

                // canvasに画像を貼り付ける
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                video.pause(); // 映像を停止

                // キャンバスの中身をPOST送信
                sendPost("img_data", canvas.toDataURL("image/png", 1));
            });

        });


        function sendPost(key, value) {
            // 現在のパスを格納、処理先
            const url = "";
            url += window.location.protocol;
            url += "//";
            url += window.location.host;
            url += window.location.pathname;

            const xhr = new XMLHttpRequest();
            const fd = new FormData();



            // 実行した後の処理
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText); // handle response.
                }
            };
            
            // POSTで実行
            xhr.open("POST", url, true);
            // urlへの送信内容 第一引数が添え字、第二引数が値
            fd.append(key, value);
            // multipart/form-data のアップロードを開始します。
            xhr.send(fd);
        }
    </script>
</body>

</html>