<?php
/**
 * @var string|null $title
 */
?>

<div class="col-12 d-flex align-items-center justify-content-center" <?php if(isset($title)): ?> style="gap: 8px" <?php endif; ?>>
    <span style="border-bottom: 1px solid #dcdcdc; flex: 1;"></span>
    <?php if(isset($title)): ?>
        <span><?= $title ?></span>
    <?php endif; ?>
    <span style="border-bottom: 1px solid #dcdcdc; flex: 1;"></span>
</div>
