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
                        <?php foreach ($allee['rangees'] as $rangee): ?>
                            <?php
                            $totalRangee = 0;
                            $rangeeOccupe = false;

                            foreach ($rangee['niveaux'] as $n) {
                                foreach ($n['cages'] as $c) {
                                    foreach ($c['emplacements'] as $e) {
                                        $totalRangee++;
                                        if ($e['statut_id'] != 1) $rangeeOccupe = true;
                                    }
                                }
                            }

                            $bgRangee = $rangeeOccupe ? '#d9534f' : '#5cb85c';
                            $firstRangee = true;
                            ?>

                            <?php foreach ($rangee['niveaux'] as $niveau): ?>
                                <?php
                                $totalNiveau = 0;
                                $niveauOccupe = false;

                                foreach ($niveau['cages'] as $c) {
                                    foreach ($c['emplacements'] as $e) {
                                        $totalNiveau++;
                                        if ($e['statut_id'] != 1) $niveauOccupe = true;
                                    }
                                }

                                $bgNiveau = $niveauOccupe ? '#d9534f' : '#5cb85c';
                                $firstNiveau = true;
                                ?>

                                <?php foreach ($niveau['cages'] as $cage): ?>

                                    <?php
                                    $totalCage = count($cage['emplacements']);
                                    $cageOccupe = false;

                                    foreach ($cage['emplacements'] as $e) {
                                        if ($e['statut_id'] != 1) $cageOccupe = true;
                                    }

                                    $bgCage = $cageOccupe ? '#d9534f' : '#5cb85c';
                                    $firstCage = true;
                                    ?>

                                    <?php foreach ($cage['emplacements'] as $emp): ?>

                                        <?php
                                        $bg = ($emp['statut_id'] == 1) ? '#5cb85c' : '#d9534f';
                                        $border = ($emp['statut_id'] == 1) ? '2px solid #5cb85c' : '2px solid #d9534f';
                                        ?>

                                        <tr style="color:white; line-height:40px;">

                                            <!-- Rangée -->
                                            <?php if ($firstRangee): $firstRangee = false; ?>
                                                <td rowspan="<?= $totalRangee ?>"
                                                    style="background:<?= $bgRangee ?>;"
                                                    class="align-middle fw-bold">
                                                    <?= $rangee['rangee_code'] ?>
                                                </td>
                                            <?php endif; ?>

                                            <!-- Niveau -->
                                            <?php if ($firstNiveau): $firstNiveau = false; ?>
                                                <td rowspan="<?= $totalNiveau ?>"
                                                    style="background:<?= $bgNiveau ?>;"
                                                    class="align-middle">
                                                    <?= $niveau['niveau_code'] ?>
                                                </td>
                                            <?php endif; ?>

                                            <!-- Cage -->
                                            <?php if ($firstCage): $firstCage = false; ?>
                                                <td rowspan="<?= $totalCage ?>"
                                                    style="background:<?= $bgCage ?>;"
                                                    class="align-middle">
                                                    <?= $cage['cage_code'] ?>
                                                </td>
                                            <?php endif; ?>

                                            <!-- Emplacement -->
                                            <td style="background:<?= $bg ?>; border-top:<?= $border ?>; border-bottom:<?= $border ?>; cursor:pointer;"
                                                onclick="mouvement('<?= $emp['qr_code_texte'] ?>', <?= $emp['statut_id'] ?>)">
                                                <?= $emp['emplacement_code'] ?>
                                            </td>

                                            <!-- Statut -->
                                            <td style="background:<?= $bg ?>; border-top:<?= $border ?>; border-bottom:<?= $border ?>; cursor:pointer;"
                                                onclick="mouvement('<?= $emp['qr_code_texte'] ?>', <?= $emp['statut_id'] ?>)">
                                                <?php if ($emp['statut_id'] == 1): ?>
                                                    <span class="badge bg-success">Libre</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Occupé</span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; // allées 
    ?>