<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Base/Register.php';
require_once __DIR__ . '/../src/Data/Xml.php';
?>
    <div class="example">
        <h3>Create xml from scratch</h3>
        <div>
            <?php require_once __DIR__ . '/xml/new_file.php'; ?>
        </div>
    </div>

    <div class="example">
        <h3>Create xml from file</h3>
        <div>
            <?php require_once __DIR__ . '/xml/exist_file.php'; ?>
        </div>
    </div>

    <div class="example">
        <h3>Errors</h3>
        <div>
            <?php require_once __DIR__ . '/xml/errors.php'; ?>
        </div>
    </div>
<?php
require_once __DIR__ . '/footer.php';
