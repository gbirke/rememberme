<?php

/**
 * Include a template file and render its output into the layout template.
 *
 * @param string $name Template name (bare, without path or suffix)
 * @param string $msg  Optional "msg" variable to be used in template
 */
function render_template($name, $msg = "")
{
    $fn = __DIR__.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR.$name.".php";
    if (file_exists($fn)) {
        ob_start();
        include $fn;
        $content = ob_get_clean();
    } else {
        $content = "Template $fn not found";
    }
    include __DIR__.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."layout.php";
}
