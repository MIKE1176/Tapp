<?php
    include("./session.php");
    check_auth(); 

    // Caricamento configurazione
    $configPath = '../config_orari.json';
    $config = json_decode(file_get_contents($configPath), true);

    $minGG = $config['preavviso_minimo_giorni'];
    $maxGG = $config['limite_massimo_giorni'];
    $slotMinuti = $config['slot_orari_minuti'];
    $mattina = $config['orari']['mattina'];
    $pomeriggio = $config['orari']['pomeriggio'];

    // Funzione PHP che genera slot dinamici
    function generaSlot($inizio, $fine, $passo) {
        $inizioSecondi = $inizio * 3600;
        $fineSecondi = $fine * 3600;
        $passoSecondi = $passo * 60;

        for($i = $inizioSecondi; $i < $fineSecondi; $i += $passoSecondi) {
            $time = date("H:i", $i);
            echo "
            <div class='col text-center'>
                <input type='radio' class='btn-check' name='oraScelta' id='t$time' value='$time' required>
                <label class='btn btn-outline-primary w-100 py-3 fw-bold shadow-none border-2' for='t$time'>$time</label>
            </div>";
        }
    }
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#0033A1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="manifest" href="manifest.json"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
    </style>
    <link rel="icon" href="../assets/favicon.ico" type="image/x-icon">
    <title>Nuova prenotazione</title>
</head>

<script>
    // Recuperiamo il valore dal PHP per usarlo in JS
    const slotDinamico = <?php echo (isset($slotMinuti)) ? $slotMinuti : 30; ?>;

    function getMinDate() {
        let d = new Date();
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() + <?php echo $minGG; ?>);
        return d;
    }

    function getMaxDate() {
        let d = new Date();
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() + <?php echo $maxGG; ?>);
        return d;
    }

    function validaData() {
        let g = parseInt(document.getElementById('giornoArrivo').value);
        let m = parseInt(document.getElementById('meseArrivo').value);
        let a = parseInt(document.getElementById('annoArrivo').value);
        
        let dataScelta = new Date(a, m - 1, g);
        let minD = getMinDate();
        let maxD = getMaxDate();
        let avviso = document.getElementById('avvisoData');

        if (dataScelta < minD || dataScelta > maxD) {
            let target = (dataScelta > maxD) ? maxD : minD;
            document.getElementById('giornoArrivo').value = target.getDate().toString().padStart(2, '0');
            document.getElementById('meseArrivo').value = (target.getMonth() + 1).toString().padStart(2, '0');
            document.getElementById('annoArrivo').value = target.getFullYear();
            
            if(avviso) {
                avviso.innerText = (dataScelta > maxD) ? "⚠️ Massimo <?php echo $maxGG; ?> giorni di anticipo" : "⚠️ Minimo <?php echo $minGG; ?> giorni di anticipo";
                avviso.classList.remove('d-none');
                setTimeout(() => avviso.classList.add('d-none'), 3000);
            }
            return false;
        }
        return true;
    }

    function validaDurata() {
        let ore = parseInt(document.getElementById('oreDurata').value);
        let min = parseInt(document.getElementById('minutiDurata').value);
        let btn = document.getElementById('btnVaiAOrario');
        let avviso = document.getElementById('avvisoDurata');

        // Se entrambi sono a 0, disabilita (durata minima deve essere almeno uno slot)
        if (ore === 0 && min === 0) {
            btn.disabled = true;
            if(avviso) {
                avviso.innerText = "⚠️ Inserire una durata!";
                avviso.classList.remove('d-none');
            }
        } else {
            btn.disabled = false;
            if(avviso) avviso.classList.add('d-none');
        }
    }

    function vaiAData(idLuogoValue) {
        document.getElementById('idLuogo').value = idLuogoValue;
        document.getElementById('faseLuogo').classList.add('d-none');
        document.getElementById('faseDataDurata').classList.remove('d-none');
        document.getElementById('bottoneIndietro').value = "faseDataDurata";
        
        let min = getMinDate();
        document.getElementById('giornoArrivo').value = min.getDate().toString().padStart(2, '0');
        document.getElementById('meseArrivo').value = (min.getMonth() + 1).toString().padStart(2, '0');
        document.getElementById('annoArrivo').value = min.getFullYear();
        
        validaDurata();
        window.scrollTo(0,0);
    }

    function vaiAOrario() {
        document.getElementById('faseDataDurata').classList.add('d-none');
        document.getElementById('faseOrario').classList.remove('d-none');
        document.getElementById('bottoneIndietro').value = "faseOrario";
        window.scrollTo(0,0);
    }

    function increase(idInput, step, min, max) {
        let input = document.getElementById(idInput);
        let val = parseInt(input.value, 10);
        
        // Se l'ID è minutiDurata, ignoriamo lo step passato nel tag e usiamo quello del JSON
        let actualStep = (idInput === 'minutiDurata') ? slotDinamico : step;

        if (val + actualStep < max) {
            input.value = (val + actualStep).toString().padStart(2, '0');
        } else {
            input.value = min.toString().padStart(2, '0');
        }
        if (idInput.includes('Arrivo')) validaData();
        validaDurata();
    }

    function decrease(idInput, step, min, max) {
        let input = document.getElementById(idInput);
        let val = parseInt(input.value, 10);
        
        let actualStep = (idInput === 'minutiDurata') ? slotDinamico : step;

        if (val - actualStep >= min) {
            input.value = (val - actualStep).toString().padStart(2, '0');
        } else {
            // Se scende sotto il minimo, ricomincia dal massimo meno uno step
            let res = max - actualStep;
            input.value = res.toString().padStart(2, '0');
        }
        if (idInput.includes('Arrivo')) validaData();
        validaDurata();
    }

    function filtraLuoghi() {
        let input = document.getElementById('searchBar');
        let filter = input.value.toLowerCase();
        let cards = document.getElementsByClassName('card-luogo');
        for (let i = 0; i < cards.length; i++) {
            let title = cards[i].querySelector('.card-title').innerText.toLowerCase();
            cards[i].classList.toggle('d-none', !title.includes(filter));
        }
    }

    function hideBack(currentStep) {
        if (currentStep == "faseLuogo") {
            window.location.href = 'home.php';
        } else if (currentStep == "faseDataDurata") {
            document.getElementById('faseDataDurata').classList.add('d-none');
            document.getElementById('faseLuogo').classList.remove('d-none');
            document.getElementById('bottoneIndietro').value = "faseLuogo";
        } else if (currentStep == "faseOrario") {
            document.getElementById('faseOrario').classList.add('d-none');
            document.getElementById('faseDataDurata').classList.remove('d-none');
            document.getElementById('bottoneIndietro').value = "faseDataDurata";
        }
    }
</script>

<body class="m-0 p-0" style="background-color: rgb(0, 51, 161); min-height: 100vh; font-family: sans-serif;">
    <div class="container-fluid p-3 pb-5">
        
        <div class="d-flex align-items-center bg-primary-subtle rounded-5 p-1 border border-3 border-white mb-4">
            <button type="button" class="btn btn-primary rounded-5 w-100 shadow-none border-0 py-2" id="bottoneIndietro" value="faseLuogo" onclick="hideBack(this.value)">
                <div class="hstack d-flex align-items-center justify-content-center">
                    <i class="bi bi-arrow-bar-left h2 m-0"></i>
                    <div class="vr mx-3"></div>
                    <span class="h2 m-0 fw-bold text-uppercase">Indietro</span>
                </div>
            </button>
        </div>

        <form action="aggiungiPrenotazione.php" method="post">
            <input type="hidden" id="idLuogo" name="idLuogo">

            <div id="faseLuogo">
                <h1 class="text-light text-center h2 mb-3 fw-bold">1: DOVE VUOI ANDARE?</h1>
                <div class="input-group mb-4">
                    <span class="input-group-text rounded-start-4 bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBar" class="form-control rounded-end-4 border-0 p-3 shadow-none" placeholder="Cerca il luogo..." onkeyup="filtraLuoghi()">
                </div>
                
                <div class="vstack gap-3">
                    <?php
                    $query = mysqli_query($db, "SELECT * FROM luogo ORDER BY denominazione ASC;");
                    while ($row = mysqli_fetch_assoc($query)) {
                        if($row['ID'] > 2){
                            $idL = $row['ID'];
                            $nomeL = $row['denominazione'];
                            echo "
                            <div class='card card-luogo border-0 rounded-4 bg-light shadow-sm mb-2'>
                                <div class='card-body text-center p-3'>
                                    <h2 class='h3 mb-3 text-dark fw-bold card-title'>$nomeL</h2>
                                    <button type='button' class='btn btn-primary btn-lg w-100 rounded-3 shadow-none fw-bold py-3 text-uppercase' onclick='vaiAData(\"$idL\")'>
                                        Scegli questo luogo
                                    </button>
                                </div>
                            </div>";
                        }
                    }
                    ?>
                </div>
            </div>

            <div id="faseDataDurata" class="d-none">
                <h1 class="text-light text-center h2 mb-3 fw-bold">2: GIORNO E DURATA</h1>
                
                <div class="bg-white p-3 rounded-5 border border-3 border-primary shadow mb-4">
                    <h4 class="text-primary text-center fw-bold mb-1">Scegli il giorno</h4>
                    <div class="row g-2">
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('giornoArrivo', 1, 1, 31)"><i class="bi bi-plus-lg h4"></i></button>
                            <input class="form-control-plaintext text-center h2 fw-bold p-0 text-dark" id="giornoArrivo" name="giornoArrivo" readonly>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('giornoArrivo', 1, 1, 31)"><i class="bi bi-dash h4"></i></button>
                            <div class="small fw-bold opacity-50">GIORNO</div>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('meseArrivo', 1, 1, 12)"><i class="bi bi-plus-lg h4"></i></button>
                            <input class="form-control-plaintext text-center h2 fw-bold p-0 text-dark" id="meseArrivo" name="meseArrivo" readonly>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('meseArrivo', 1, 1, 12)"><i class="bi bi-dash h4"></i></button>
                            <div class="small fw-bold opacity-50">MESE</div>
                        </div>
                        <div class="col-4 text-center">
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('annoArrivo', 1, 2024, 2030)"><i class="bi bi-plus-lg h4"></i></button>
                            <input class="form-control-plaintext text-center h2 fw-bold p-0 text-dark" id="annoArrivo" name="annoArrivo" readonly>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('annoArrivo', 1, 2024, 2030)"><i class="bi bi-dash h4"></i></button>
                            <div class="small fw-bold opacity-50">ANNO</div>
                        </div>
                    </div>
                    <p id="avvisoData" class="text-danger text-center fw-bold small mt-2 d-none"></p>
                </div>

                <div class="bg-white p-3 rounded-5 border border-3 border-primary shadow mb-4">
                    <h4 class="text-primary text-center fw-bold mb-3">Quanto tempo starai?</h4>
                    <div class="row g-2">
                        <div class="col-6 text-center">
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('oreDurata', 1, 0, 10)"><i class="bi bi-plus-lg h3"></i></button>
                            <input class="form-control-plaintext text-center h1 fw-bold p-0 text-dark" id="oreDurata" name="oreDurata" readonly value="01">
                            <div class="small fw-bold opacity-50">ORE</div>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('oreDurata', 1, 0, 10)"><i class="bi bi-dash h3"></i></button>
                        </div>
                        <div class="col-6 text-center">
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('minutiDurata', 0, 0, 60)"><i class="bi bi-plus-lg h3"></i></button>
                            <input class="form-control-plaintext text-center h1 fw-bold p-0 text-dark" id="minutiDurata" name="minutiDurata" readonly value="00">
                            <div class="small fw-bold opacity-50">MINUTI</div>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('minutiDurata', 0, 0, 60)"><i class="bi bi-dash h3"></i></button>
                        </div>
                    </div>
                    <p id="avvisoDurata" class="text-danger text-center fw-bold h5 mt-2 d-none"></p>
                </div>

                <button type="button" id="btnVaiAOrario" class="btn btn-warning w-100 py-4 fs-2 rounded-4 border border-4 border-white shadow-lg fw-bold text-uppercase" onclick="vaiAOrario()">
                    Prosegui <i class="bi bi-arrow-right"></i>
                </button>
            </div>

            <div id="faseOrario" class="d-none">
                <h1 class="text-light text-center h2 mb-1 fw-bold">3: ORA DI ARRIVO</h1>
                <p class="text-white text-center h5 mb-4 opacity-75">A che ora vuoi arrivare lì?</p>
                
                <div class="bg-white p-3 rounded-5 border border-3 border-primary shadow">
                    <h5 class="text-primary fw-bold text-center border-bottom pb-2 text-uppercase">Mattina</h5>
                    <div class="row row-cols-3 g-2 mb-4">
                        <?php generaSlot($mattina['inizio'], $mattina['fine'], $slotMinuti); ?>
                    </div>

                    <h5 class="text-primary fw-bold text-center border-bottom pb-2 text-uppercase">Pomeriggio</h5>
                    <div class="row row-cols-3 g-2 mb-2">
                        <?php generaSlot($pomeriggio['inizio'], $pomeriggio['fine'], $slotMinuti); ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-4 fs-1 mt-5 mb-5 rounded-4 border border-5 border-white shadow-lg fw-bold text-uppercase">
                    Salva Prenotazione
                </button>
            </div>
        </form>
    </div>
</body>
</html>