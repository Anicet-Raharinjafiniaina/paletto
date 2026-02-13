<div>
    <label for="code_txt">Créer un code QR</label>
    <input type="text" id="code_txt">
    <button id="btn_creer">Créer</button>
</div>

<br><br><br>

<div id="reader" style="width:300px;"></div>
<br><br>
<div id="result"></div>

<script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/js/qr_code/qr_code.min.js"></script>

<script>
    var urlProject = "<?= base_url(); ?>";

    $('#btn_creer').on('click', function() {
        var text = encodeURIComponent($('#code_txt').val());
        if (!text) return alert("Veuillez entrer un texte.");

        // Naviguer vers l'URL qui génère le QR code
        window.location.href = urlProject + 'QrCodeController/generate?text=' + text;
    });
</script>


<script>
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('result').innerHTML = "QR détecté : <b>" + decodedText + "</b>";
        $.ajax({
            url: 'QrCodeController/scan',
            type: 'POST',
            dataType: 'json',
            data: {
                data: decodedText
            },
            success: function(data) {
                console.log("réussi");
            }
        })

    }

    function onScanFailure(error) {
        console.warn(`Erreur de scan : ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", {
            fps: 10,
            qrbox: 250
        },
        false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>