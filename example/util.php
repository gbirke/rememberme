<?php




function render_template($name, $msg="") {
    $fn = __DIR__ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $name . ".php";
    if(file_exists($fn)) {
        ob_start();
        include $fn;
        $content = ob_get_clean();

    }
    else {
        $content = "Template $fn not found";
    }
    include __DIR__ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "layout.php";
}