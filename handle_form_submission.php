<?php
// Includi il file wp-load.php per avere accesso alle funzioni di WordPress
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Abilita il log degli errori e imposta il percorso del file di log
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt'); // Percorso del file di log

// Log di debug per verificare l'esecuzione dello script
error_log('Form submission received - Start of script');

// Verifica se i dati sono stati inviati tramite POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST data: ' . print_r($_POST, true));

    // Ottieni i dati inviati dal modulo
    $nome = isset($_POST['Name']) ? sanitize_text_field($_POST['Name']) : '';
    $cognome = isset($_POST['Cognome']) ? sanitize_text_field($_POST['Cognome']) : '';
    $telefono = isset($_POST['Telefono']) ? sanitize_text_field($_POST['Telefono']) : '';
    $legame = isset($_POST['Legame_con_']) ? sanitize_text_field($_POST['Legame_con_']) : '';
    $data_matrimonio = isset($_POST['Data_matrimonio']) ? sanitize_text_field($_POST['Data_matrimonio']) : '';
    $sacramenti = isset($_POST['Sacramenti_ricevuti']) ? array_map('sanitize_text_field', explode(',', $_POST['Sacramenti_ricevuti'])) : array();

    // Gestione manuale dei campi ripetibili per i figli
    $figli_eventuali = [];
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_POST["Nome_Figlio/a_$i"]) && !empty($_POST["Età_Figlio/a_{$i}_"])) {
            $figli_eventuali[] = array(
                'nome_del_figlioa' => sanitize_text_field($_POST["Nome_Figlio/a_$i"]),
                'select_eta' => intval($_POST["Età_Figlio/a_{$i}_"])
            );
        }
    }

    // Log dei dati raccolti
    error_log('Nome: ' . $nome);
    error_log('Cognome: ' . $cognome);
    error_log('Telefono: ' . $telefono);
    error_log('Legame: ' . $legame);
    error_log('Data Matrimonio: ' . $data_matrimonio);
    error_log('Sacramenti: ' . print_r($sacramenti, true));
    error_log('Figli Eventuali: ' . print_r($figli_eventuali, true));

    // Crea un nuovo post
    $post_data = array(
        'post_title' => $nome . ' ' . $cognome,
        'post_status' => 'publish',
        'post_type' => 'discente', // Cambia 'discente' con 'post', 'page' o il tuo CPT
    );

    $new_post_id = wp_insert_post($post_data);

    // Verifica se il post è stato creato correttamente
    if (!is_wp_error($new_post_id)) {
        // Salva i campi personalizzati
        update_post_meta($new_post_id, 'text_nome', $nome);
        update_post_meta($new_post_id, 'text_cognome', $cognome);
        update_post_meta($new_post_id, 'text_telefono', $telefono);
        update_post_meta($new_post_id, 'text_legame', $legame);
        update_post_meta($new_post_id, 'data_matrimonio', $data_matrimonio);
        update_post_meta($new_post_id, 'sacramenti_ricevuti', $sacramenti);
        update_post_meta($new_post_id, 'text_figli_eventuali', $figli_eventuali);

        // Verifica se i metadati sono stati salvati correttamente
        error_log('Post meta saved for ID: ' . $new_post_id);
        error_log('Nome Meta: ' . get_post_meta($new_post_id, 'text_nome', true));
        error_log('Cognome Meta: ' . get_post_meta($new_post_id, 'text_cognome', true));
        error_log('Telefono Meta: ' . get_post_meta($new_post_id, 'text_telefono', true));
        error_log('Legame Meta: ' . get_post_meta($new_post_id, 'text_legame', true));
        error_log('Data Matrimonio Meta: ' . get_post_meta($new_post_id, 'data_matrimonio', true));
        error_log('Sacramenti Meta: ' . print_r(get_post_meta($new_post_id, 'sacramenti_ricevuti', true), true));
        error_log('Figli Eventuali Meta: ' . print_r(get_post_meta($new_post_id, 'text_figli_eventuali', true), true));
    } else {
        error_log('Error creating/updating post: ' . $new_post_id->get_error_message());
    }
} else {
    error_log('Request method is not POST');
    error_log('Request method received: ' . $_SERVER['REQUEST_METHOD']);
}

error_log('Form submission received - End of script');
?>