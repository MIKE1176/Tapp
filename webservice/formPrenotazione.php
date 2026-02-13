<?php
    include("./session.php");
    check_auth(); 

    // Caricamento configurazione
    $configPath = '../config_orari.json';
    $config = json_decode(file_get_contents($configPath), true);

    $minGG = $config['preavviso_minimo_giorni'];
    $maxGG = $config['limite_massimo_giorni'];

    $giorniAttivi = $config['giorni_attivi']; // es: [1, 2, 3, 4, 5]

    $slotDurata = $config['slot_orari_minuti'];         // Step 2
    $slotPrenotazione = $config['slot_prenotazioni'];   // Step 3

    $mattina = $config['orari']['mattina'];
    $pomeriggio = $config['orari']['pomeriggio'];

    // Funzione PHP che genera slot dinamici
    function generaSlot($inizio, $fine, $passo, $periodo) {
        $inizioSecondi = $inizio * 3600;
        $fineSecondi = $fine * 3600;
        $passoSecondi = $passo * 60;

        for($i = $inizioSecondi; $i < $fineSecondi; $i += $passoSecondi) {
            $time = date("H:i", $i);
            $limiteMinuti = $fine * 60; 
            echo "
            <div class='col text-center slot-wrapper'>
                <input type='radio' class='btn-check slot-input' name='oraScelta' 
                       id='t$time' value='$time' data-fine='$limiteMinuti' data-periodo='$periodo' required>
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
        .slot-input:disabled + label {
            opacity: 0.35;
            pointer-events: none;
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            border-style: dashed !important;
        }
        .slot-wrapper label {
            transition: all 0.2s ease-in-out;
        }
        
        #alertServizioOff {
            border: 3px solid #dc3545 !important;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(5px); }
            50% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
            100% { transform: translateX(0); }
        }
    </style>
    <link rel="icon" href="../assets/favicon.ico" type="image/x-icon">
    <title>Nuova prenotazione</title>
</head>

<script>
    const controlloFineTurno = {
        mattina: false,    // Metti TRUE se vuoi che anche la mattina sia bloccata se sfora
        pomeriggio: true   // Metti TRUE per bloccare il pomeriggio (default)
    };

    const slotDurata = <?php echo $slotDurata; ?>;
    const giorniAttivi = <?php echo json_encode($giorniAttivi); ?>;
    let servizioAttivo = true;
    const nomiMesi = ["GENNAIO", "FEBBRAIO", "MARZO", "APRILE", "MAGGIO", "GIUGNO", "LUGLIO", "AGOSTO", "SETTEMBRE", "OTTOBRE", "NOVEMBRE", "DICEMBRE"];
    const nomiGiorni = ["DOMENICA", "LUNEDÌ", "MARTEDÌ", "MERCOLEDÌ", "GIOVEDÌ", "VENERDÌ", "SABATO"];

    function aggiornaMeseTesto() {
        let m = parseInt(document.getElementById('meseArrivo').value);
        if(!isNaN(m)) document.getElementById('meseArrivoDisplay').value = nomiMesi[m - 1];
    }

    function verificaGiornoServizio() {
        let g = parseInt(document.getElementById('giornoArrivo').value);
        let m = parseInt(document.getElementById('meseArrivo').value);
        let a = parseInt(document.getElementById('annoArrivo').value);
        
        let d = new Date(a, m - 1, g);
        let indiceGiorno = d.getDay(); // 0 = Domenica, 1 = Lunedì, ecc.
        
        // Per il controllo con l'array PHP [1,2,3,4,5] dove 7 è Domenica
        let giornoISO = (indiceGiorno === 0) ? 7 : indiceGiorno;

        let avviso = document.getElementById('alertServizioOff');
        let motivoGiorno = document.getElementById('motivoGiorno');
        let btn = document.getElementById('btnVaiAOrario');
        let sezioneDurata = document.getElementById('sezioneDurata'); // Il blocco "Quanto tempo starai"

        if (giorniAttivi.includes(giornoISO)) {
            // --- SERVIZIO ATTIVO ---
            servizioAttivo = true;
            avviso.classList.add('d-none');
            sezioneDurata.classList.remove('d-none'); // Mostra la durata
            validaDurata(); 
        } else {
            // --- SERVIZIO NON ATTIVO ---
            servizioAttivo = false;
            
            // Inseriamo il nome del giorno nell'alert
            motivoGiorno.innerText = nomiGiorni[indiceGiorno];
            
            avviso.classList.remove('d-none');
            sezioneDurata.classList.add('d-none'); // Nasconde la scelta della durata e orari
            btn.disabled = true;
        }
    }

    // --- CALENDARIO CORE ---
    function changeDate(part, delta) {
        let g = parseInt(document.getElementById('giornoArrivo').value);
        let m = parseInt(document.getElementById('meseArrivo').value);
        let a = parseInt(document.getElementById('annoArrivo').value);
        
        // Creiamo l'oggetto data corrente basato sugli input
        let d = new Date(a, m - 1, g);

        // Applichiamo la modifica richiesta
        if (part === 'giornoArrivo') d.setDate(d.getDate() + delta);
        else if (part === 'meseArrivo') d.setMonth(d.getMonth() + delta);
        else if (part === 'annoArrivo') d.setFullYear(d.getFullYear() + delta);

        let minD = getMinDate();
        let maxD = getMaxDate();
        let avviso = document.getElementById('avvisoData');

        // CONTROLLO LIMITI IMMEDIATO
        if (d < minD) {
            d = minD; // Blocca al minimo
            if(avviso) {
                avviso.innerText = "⚠️ Minimo <?php echo $minGG; ?> giorni di preavviso";
                avviso.classList.remove('d-none');
                setTimeout(() => avviso.classList.add('d-none'), 3000);
            }
        } else if (d > maxD) {
            d = maxD; // Blocca al massimo
            if(avviso) {
                avviso.innerText = "⚠️ Massimo <?php echo $maxGG; ?> giorni di anticipo";
                avviso.classList.remove('d-none');
                setTimeout(() => avviso.classList.add('d-none'), 3000);
            }
        }

        // Aggiorna fisicamente i campi nel form
        document.getElementById('giornoArrivo').value = d.getDate().toString().padStart(2, '0');
        document.getElementById('meseArrivo').value = (d.getMonth() + 1).toString().padStart(2, '0');
        document.getElementById('annoArrivo').value = d.getFullYear();

        aggiornaMeseTesto();

        verificaGiornoServizio();
    }

    // --- DURATA ---
    function changeDurata(part, delta) {
        let el = document.getElementById(part);
        let val = parseInt(el.value);
        if (part === 'oreDurata') {
            val = (val + delta + 11) % 11;
        } else {
            val = (val + (delta * slotDurata) + 60) % 60;
        }
        el.value = val.toString().padStart(2, '0');
        validaDurata();
        aggiornaSlotDisponibili();
    }

    // --- BLOCKING LOGIC (CORE) ---
    function aggiornaSlotDisponibili() {
        const ore = parseInt(document.getElementById('oreDurata').value) || 0;
        const min = parseInt(document.getElementById('minutiDurata').value) || 0;
        const durataTotale = (ore * 60) + min;

        document.querySelectorAll('.slot-input').forEach(input => {
            const periodo = input.dataset.periodo; // 'mattina' o 'pomeriggio'

            // Se il controllo per questo specifico periodo è disattivato, abilita sempre
            if (!controlloFineTurno[periodo]) {
                input.disabled = false;
                return;
            }

            // Altrimenti calcola se sfora
            const oraInizio = input.value.split(':');
            const inizioMinuti = (parseInt(oraInizio[0]) * 60) + parseInt(oraInizio[1]);
            const limiteFine = parseInt(input.dataset.fine);

            if (inizioMinuti + durataTotale > limiteFine) {
                input.disabled = true;
                input.checked = false;
            } else {
                input.disabled = false;
            }
        });
    }

    function increase(id, delta, min, max) { 
        if (id.includes('Arrivo')) {
            changeDate(id, 1);
        } else {
            changeDurata(id, 1);
        }
    }

    function decrease(id, delta, min, max) { 
        if (id.includes('Arrivo')) {
            changeDate(id, -1);
        } else {
            changeDurata(id, -1);
        }
    }

    // --- VALIDAZIONI ---

    function getMinDate() { 
        let d = new Date(); d.setHours(0,0,0,0); 
        d.setDate(d.getDate() + <?php echo $minGG; ?>); return d; 
    }

    function getMaxDate() { 
        let d = new Date(); d.setHours(0,0,0,0); 
        d.setDate(d.getDate() + <?php echo $maxGG; ?>); return d; 
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
            // Determiniamo se ha superato il massimo o il minimo
            let troppoTardi = dataScelta > maxD;
            let target = troppoTardi ? maxD : minD;

            // Riportiamo i valori dell'input entro i limiti
            document.getElementById('giornoArrivo').value = target.getDate().toString().padStart(2, '0');
            document.getElementById('meseArrivo').value = (target.getMonth() + 1).toString().padStart(2, '0');
            document.getElementById('annoArrivo').value = target.getFullYear();
            
            // Aggiorniamo il testo del mese (es: da "02" a "FEBBRAIO")
            aggiornaMeseTesto();

            if (avviso) {
                // Messaggio personalizzato con i giorni configurati nel JSON
                avviso.innerText = troppoTardi 
                    ? "⚠️ Massimo <?php echo $maxGG; ?> giorni di anticipo" 
                    : "⚠️ Minimo <?php echo $minGG; ?> giorni di anticipo";
                
                avviso.classList.remove('d-none');
                
                // Nascondi l'avviso dopo 3 secondi
                setTimeout(() => {
                    avviso.classList.add('d-none');
                }, 3000);
            }
            return false;
        }
        return true;
    }

    function validaDurata() {
        let ore = parseInt(document.getElementById('oreDurata').value);
        let min = parseInt(document.getElementById('minutiDurata').value);
        let btn = document.getElementById('btnVaiAOrario');
        if (!servizioAttivo) {
            btn.disabled = true;
            return;
        }
        btn.disabled = (ore === 0 && min === 0);
    }

    // --- NAVIGAZIONE ---
    function vaiAData(idLuogoValue) {
        document.getElementById('idLuogo').value = idLuogoValue;
        document.getElementById('faseLuogo').classList.add('d-none');
        document.getElementById('faseDataDurata').classList.remove('d-none');
        document.getElementById('bottoneIndietro').value = "faseDataDurata";
        
        let min = getMinDate();
        document.getElementById('giornoArrivo').value = min.getDate().toString().padStart(2, '0');
        document.getElementById('meseArrivo').value = (min.getMonth() + 1).toString().padStart(2, '0');
        document.getElementById('annoArrivo').value = min.getFullYear();
        aggiornaMeseTesto();
        verificaGiornoServizio();
        validaDurata();
        window.scrollTo(0,0);
    }

    function vaiAOrario() {
        document.getElementById('faseDataDurata').classList.add('d-none');
        document.getElementById('faseOrario').classList.remove('d-none');
        document.getElementById('bottoneIndietro').value = "faseOrario";
        aggiornaSlotDisponibili();
        window.scrollTo(0,0);
    }

    function hideBack(currentStep) {
        if (currentStep == "faseLuogo") window.location.href = 'home.php';
        else if (currentStep == "faseDataDurata") {
            document.getElementById('faseDataDurata').classList.add('d-none');
            document.getElementById('faseLuogo').classList.remove('d-none');
            document.getElementById('bottoneIndietro').value = "faseLuogo";
        } else {
            document.getElementById('faseOrario').classList.add('d-none');
            document.getElementById('faseDataDurata').classList.remove('d-none');
            document.getElementById('bottoneIndietro').value = "faseDataDurata";
        }
    }

    function filtraLuoghi() {
        let filter = document.getElementById('searchBar').value.toLowerCase();
        let cards = document.getElementsByClassName('card-luogo');
        for (let card of cards) {
            let title = card.querySelector('.card-title').innerText.toLowerCase();
            card.classList.toggle('d-none', !title.includes(filter));
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
                        <div class="col-3 text-center">
                            <div class="small fw-bold opacity-50">GIORNO</div>
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('giornoArrivo', 1, 1, 31)"><i class="bi bi-plus-lg h4"></i></button>
                            <input class="form-control-plaintext text-center h2 fw-bold p-0 text-dark" id="giornoArrivo" name="giornoArrivo" readonly>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('giornoArrivo', 1, 1, 31)"><i class="bi bi-dash h4"></i></button>
                        </div>

                        <div class="col-6 text-center">
                            <div class="small fw-bold opacity-50">MESE</div>
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('meseArrivo', 1, 1, 13)"><i class="bi bi-plus-lg h4"></i></button>
                            <input type="hidden" id="meseArrivo" name="meseArrivo">
                            <input class="form-control-plaintext text-center h2 fw-bold p-0 text-dark" id="meseArrivoDisplay" readonly>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('meseArrivo', 1, 1, 13)"><i class="bi bi-dash h4"></i></button>
                        </div>

                        <div class="col-3 text-center">
                            <div class="small fw-bold opacity-50">ANNO</div>
                            <button type="button" class="btn btn-primary w-100 mb-2 shadow-none" onclick="increase('annoArrivo', 1, 2026, 2030)"><i class="bi bi-plus-lg h4"></i></button>
                            <input class="form-control-plaintext text-center h2 fw-bold p-0 text-dark" id="annoArrivo" name="annoArrivo" readonly>
                            <button type="button" class="btn btn-primary w-100 mt-2 shadow-none" onclick="decrease('annoArrivo', 1, 2026, 2030)"><i class="bi bi-dash h4"></i></button>
                        </div>
                    </div>
                </div>

                <div id="avvisoData" class="alert alert-warning text-center fw-bold d-none rounded-4 border-0 mt-3 mb-3 shadow">
                    Avviso Data
                </div>

                <div id="alertServizioOff" class="alert alert-danger text-center fw-bold d-none rounded-4 border-0 mt-3 mb-3 shadow p-4">
                    <i class="bi bi-calendar-x h1 d-block mb-2"></i>
                    SERVIZIO NON ATTIVO<br>
                    PERCHÉ È <span id="motivoGiorno"></span>
                </div>

                <div id="sezioneDurata">
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
                    </div>

                    <button type="button" id="btnVaiAOrario" class="btn btn-warning w-100 py-4 fs-2 rounded-4 border border-4 border-white shadow-lg fw-bold text-uppercase" onclick="vaiAOrario()">
                        Prosegui <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div id="faseOrario" class="d-none">
                <h1 class="text-light text-center h2 mb-1 fw-bold">3: A CHE ORA DEVI ARRIVARE?</h1>
                <p class="text-white text-center h5 mb-4 opacity-75">Inserisci l'ora di arrivo</p>
                
                <div class="bg-white p-3 rounded-5 border border-3 border-primary shadow">
                    <h5 class="text-primary fw-bold text-center border-bottom pb-2 text-uppercase">Mattina</h5>
                    <div class="row row-cols-3 g-2 mb-4">
                        <?php generaSlot($mattina['inizio'], $mattina['fine'], $slotPrenotazione, 'mattina'); ?>
                    </div>

                    <h5 class="text-primary fw-bold text-center border-bottom pb-2 text-uppercase">Pomeriggio</h5>
                    <div class="row row-cols-3 g-2 mb-2">
                        <?php generaSlot($pomeriggio['inizio'], $pomeriggio['fine'], $slotPrenotazione, 'pomeriggio'); ?>
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