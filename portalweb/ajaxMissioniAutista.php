<?php
include("./session.php");
check_auth();

$idAutista=$_SESSION['ID'];

$data=$_GET['date'] ?? null;
if(!$data) exit;
$sql="
SELECT m.ID,
       m.tipo,
       m.data,
       m.durata,
       u.nome,
       u.cognome,
       l.denominazione luogo,
       TIME(m.data) ora
FROM missione m

JOIN utente u ON m.id_utente=u.ID
LEFT JOIN luogo l ON m.id_destinazione=l.ID

WHERE m.statoCompilazione='INSERITA'
AND DATE(m.data)=?

AND EXISTS (
   SELECT 1
   FROM turno t
   WHERE t.id_operatore=?
   AND m.data BETWEEN t.dataInizio AND t.dataFine
)

AND NOT EXISTS (
   SELECT 1
   FROM missione mx
   JOIN turno tx ON mx.id_turno = tx.ID
   WHERE tx.id_operatore = ?
   AND DATE(mx.data)=DATE(m.data)
   AND (
        m.data < ADDTIME(
                   mx.data,
                   IF(mx.durata='00:00:00','00:30:00',mx.durata)
                 )
        AND ADDTIME(
              m.data,
              IF(m.durata='00:00:00','00:30:00',m.durata)
            ) > mx.data
   )
)

ORDER BY m.data
";

$stmt=$db->prepare($sql);
$stmt->bind_param("sii",$data,$idAutista,$idAutista);
$stmt->execute();
$res=$stmt->get_result();

while($r=$res->fetch_assoc()){

 echo "
 <div class='col-12'>
  <div class='card card-missione shadow p-3'>

   <h5>{$r['cognome']} {$r['nome']}</h5>

   <div class='small text-muted'>{$r['tipo']} – {$r['ora']}</div>

   <p class='mt-2 mb-1'><b>Luogo:</b> {$r['luogo']}</p>

   <button class='btn btn-success w-100'
           onclick='assegnaMissione({$r['ID']})'>
           Accetta missione
   </button>

  </div>
 </div>";
}
