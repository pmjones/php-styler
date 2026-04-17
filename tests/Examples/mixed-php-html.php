<?php $x = 1; ?>
<div>
    <p><?= $x ?></p>
    <p><?= htmlspecialchars($longVariableName) ?></p>
    <script>var data = <?= json_encode($everything) ?>;</script>
    <?php  if ($show): ?>
        <span>visible</span>
    <?php endif; ?>
    <?php  if ($a) { ?>
        <span>yes</span>
    <?php  } else { ?>
        <span>no</span>
    <?php } ?>
</div>
<?php  echo "done";
