 <div class="row plan">
     <div class="col-12">
         <div class="container-fluid mt-3">
             <?php foreach ($tree as $alleeId => $allee): ?>
                 <div class="card">
                     <div class="card-header">
                         <!-- ALLÉE -->
                         <h5 class="px-3 py-2 rounded mt-4">
                             ➡️ ALLÉE : <?= htmlspecialchars($allee['allee_code']) ?>
                         </h5>
                     </div>
                     <div class="card-body">
                         <table class="table table-bordered table-black-border table-sm text-center">
                             <thead class="bg-dark text-white">
                                 <tr>
                                     <th>RANGÉE</th>
                                     <th>NIVEAU</th>
                                     <th>CAGE / ALVÉOLE</th>
                                     <th>EMPLACEMENT</th>
                                     <th>STATUT</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php foreach ($allee['rangees'] as $rangeeId => $rangee):
                                        $totalRangee = 0;
                                        foreach ($rangee['niveaux'] as $n)
                                            foreach ($n['cages'] as $c)
                                                $totalRangee += count($c['emplacements']);
                                        $firstRangee = true;
                                    ?>

                                     <?php foreach ($rangee['niveaux'] as $niveauId => $niveau):
                                            $totalNiveau = 0;
                                            foreach ($niveau['cages'] as $c)
                                                $totalNiveau += count($c['emplacements']);
                                            $firstNiveau = true;
                                        ?>

                                         <?php foreach ($niveau['cages'] as $cageId => $cage):
                                                $totalCage = count($cage['emplacements']);
                                                $firstCage = true;
                                            ?>

                                             <?php foreach ($cage['emplacements'] as $emp): ?>
                                                 <tr style="background-color: <?= $emp['statut_id'] == 1 ? '#5cb85c' : '#d9534f' ?>; color: white;">
                                                     <!-- Rangée : rowspan sur toute la rangée -->
                                                     <?php if ($firstRangee): $firstRangee = false; ?>
                                                         <td rowspan="<?= $totalRangee ?>" class="align-middle fw-bold">
                                                             <?= htmlspecialchars($rangee['rangee_code']) ?>
                                                         </td>
                                                     <?php endif; ?>

                                                     <!-- Niveau : rowspan sur tout le niveau -->
                                                     <?php if ($firstNiveau): $firstNiveau = false; ?>
                                                         <td rowspan="<?= $totalNiveau ?>" class="align-middle">
                                                             <?= htmlspecialchars($niveau['niveau_code']) ?>
                                                         </td>
                                                     <?php endif; ?>

                                                     <!-- Cage : rowspan sur toute la cage -->
                                                     <?php if ($firstCage): $firstCage = false; ?>
                                                         <td rowspan="<?= $totalCage ?>" class="align-middle">
                                                             <?= htmlspecialchars($cage['cage_code']) ?>
                                                         </td>
                                                     <?php endif; ?>

                                                     <td onclick="mouvement('<?= $emp['qr_code_texte'] ?>', <?= $emp['statut_id'] ?>)" style="cursor:pointer;"><?= htmlspecialchars($emp['emplacement_code']) ?></td>
                                                     <td onclick="mouvement('<?= $emp['qr_code_texte'] ?>', <?= $emp['statut_id'] ?>)" style="cursor:pointer;">
                                                         <?php if ($emp['statut_id'] == 1): ?>
                                                             <span class="badge bg-success">Libre</span>
                                                         <?php else: ?>
                                                             <span class="badge bg-danger">Occupé</span>
                                                         <?php endif; ?>
                                                     </td>
                                                 </tr>
                                             <?php endforeach; // emplacements 
                                                ?>

                                         <?php endforeach; // cages 
                                            ?>
                                     <?php endforeach; // niveaux 
                                        ?>
                                 <?php endforeach; // rangees 
                                    ?>

                             </tbody>
                         </table>
                     </div>
                 </div>
             <?php endforeach; // allées 
                ?>
         </div>
     </div>
 </div>