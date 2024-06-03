<?php
/*
Plugin Name: PDF Search Plugin
Description: Plugin zum Durchsuchen von PDF-Dateien mit pdfgrep
Version: 1.0
Author: Jan Hempel
*/

// Funktion zum Durchsuchen von PDF-Dateien
function search_pdfs($search_term) {
    $pdf_directories = [
        'QMH' => '/var/www/html/qm',
        'VA' => '/var/www/html/va'
    ];

    $results = [];

    foreach ($pdf_directories as $label => $dir) {
        $command = escapeshellcmd("pdfgrep -ir '$search_term' $dir");
        $output = shell_exec($command);

        // Entfernen des spezifischen Verzeichnispfads und des nachfolgenden Schrägstrichs aus der Ausgabe
        $formatted_output = str_replace($dir . '/', '', $output);
        $results[$label] = $formatted_output;
    }

    return $results;
}

// AJAX-Handler-Funktion
function pdf_search_ajax_handler() {
    if (isset($_POST['search_term'])) {
        $search_term = sanitize_text_field($_POST['search_term']);
        $results = search_pdfs($search_term);

        $output = '';

        foreach ($results as $label => $result) {
            $output .= '<h2>' . strtoupper($label) . '</h2>';
            if ($result) {
                $output .= nl2br($result);
            } else {
                $output .= 'Keine Ergebnisse gefunden.';
            }
        }

        echo $output;
    }
    wp_die();
}

add_action('wp_ajax_pdf_search', 'pdf_search_ajax_handler');
add_action('wp_ajax_nopriv_pdf_search', 'pdf_search_ajax_handler');

// Funktion zum Einfügen des Suchformulars und der Ergebnisse
function pdf_search_form_shortcode() {
    ob_start();
    ?>
    <form id="pdf_search_form" method="post" action="">
        <input placeholder="Suchbegriff" pattern=".{3,}" required title="minimum 3 Zeichen" type="text" name="pdf_search_term" id="pdf_search_term"><br><br>
        <input type="submit" value="Suchen">
    </form>
    <div id="pdf_search_results"></div>
    <?php
    return ob_get_clean();
}

add_shortcode('pdf_search_form', 'pdf_search_form_shortcode');

// JavaScript und CSS einbinden
function pdf_search_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('pdf_search_script', plugins_url('pdf-search.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('pdf_search_script', 'pdf_search_params', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
    wp_enqueue_style('pdf_search_style', plugins_url('pdf-search.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'pdf_search_enqueue_scripts');
?>
