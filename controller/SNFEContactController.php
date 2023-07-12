<?php


class SNFEContactController extends SNFEMainController {
    public static function index(){
        global $wpdb;

        $start_date = (isset($_GET['from_date']) && !empty($_GET['from_date'])) ? $_GET['from_date'] : null;
        $end_date = (isset($_GET['to_date']) && !empty($_GET['to_date'])) ? date('Y-m-d', strtotime("+1 day", strtotime($_GET['to_date']))) : date('Y-m-d', strtotime("+1 day"));

        $contact_forms =  $wpdb->get_results("SELECT forms.id, forms.form_title, COUNT(entries.form_id) AS total_entries 
        FROM {$wpdb->prefix}nf3_forms AS forms
        INNER JOIN {$wpdb->prefix}nf_entries AS entries
        ON entries.form_id = forms.id
        WHERE entries.created_at >= '$start_date' 
        AND entries.created_at <= '$end_date'
        GROUP BY forms.id
        " );

        parent::snfe_query_var_custom(['data'=> $contact_forms ]);
        load_template( SNFE_LIB_PATH. '/views/entries.php' );
    }

    public static function form_entry_details () {
        global $wpdb;
        $start_date = (isset($_GET['from_date']) && !empty($_GET['from_date'])) ? $_GET['from_date'] : null;
        $end_date = (isset($_GET['to_date']) && !empty($_GET['to_date'])) ? date('Y-m-d', strtotime("+1 day", strtotime($_GET['to_date']))) : date('Y-m-d', strtotime("+1 day"));
        
        $formid = $_GET['form']; 
        if(isset($formid) && !empty($formid)) {

            $entries =  $wpdb->get_results("SELECT *
            FROM {$wpdb->prefix}nf_entries AS entries
            LEFT JOIN {$wpdb->prefix}nf_entry_meta AS entrymeta
            ON entries.id = entrymeta.nf_entry_id
            WHERE entries.form_id = $formid
            AND entries.created_at >= '$start_date' 
            AND entries.created_at <= '$end_date';
            " );

            parent::snfe_query_var_custom(['data'=> $entries ]);
            load_template( SNFE_LIB_PATH. '/views/entry-details.php' );
        }
        else {
            wp_die('Sorry, you are not allowed to access this page.', 'Access Denied', array('response' => 403));
        }
    } 
}
