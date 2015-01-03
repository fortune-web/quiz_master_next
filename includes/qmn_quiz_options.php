<?php
/*
This page allows for the editing of quizzes selected from the quiz admin page.
*/
/* 
Copyright 2014, My Local Webstop (email : fpcorso@mylocalwebstop.com)
*/

function mlw_generate_quiz_options()
{
	global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = $_GET["quiz_id"];
	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $_GET["quiz_id"]));
	}	
	?>
	
	<script type="text/javascript"
	  src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
	</script>
	<!-- css -->
	<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/redmond/jquery-ui.css" rel="stylesheet" />
	<!-- jquery scripts -->
	<?php
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-effects-blind' );
	wp_enqueue_script( 'jquery-effects-explode' );
	?>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		// increase the default animation speed to exaggerate the effect
		$j.fx.speeds._default = 1000;
		$j(function() {
			$j( "#tabs" ).tabs();
		});
	</script>
	<style>
		.mlw_tab_content
		{
			padding: 20px 20px 20px 20px;
			margin: 20px 20px 20px 20px;
		}
	</style>
	<div class="wrap">
	<div class='mlw_quiz_options'>
	<h2>Quiz Settings For <?php echo $mlw_quiz_options->quiz_name; ?></h2>
	<?php
	ob_start();
	if ($quiz_id != "")
	{
	?>
	<div id="tabs">
		<ul>
			<?php do_action('mlw_qmn_options_tab'); ?>
		</ul>
		<?php do_action('mlw_qmn_options_tab_content'); ?>
  		
	</div>
	<?php
	}
	else
	{
		?>
		<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong>Hey!</strong> Please go to the quizzes page and click on the Edit link from the quiz you wish to edit.</p
		</div>
		<?php
	}
	$mlw_output = ob_get_contents();
	ob_end_clean();
	$mlwQuizMasterNext->alertManager->showAlerts();
	echo mlw_qmn_show_adverts();
	echo $mlw_output;
	?>
	</div>
	</div>
<?php
}

add_action('mlw_qmn_options_tab', 'mlw_options_questions_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_text_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_option_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_leaderboard_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_certificate_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_emails_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_results_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_styling_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_tools_tab');
add_action('mlw_qmn_options_tab', 'mlw_options_preview_tab');
add_action('mlw_qmn_options_tab_content', 'mlw_options_questions_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_text_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_option_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_leaderboard_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_certificate_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_emails_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_results_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_styling_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_tools_tab_content');
add_action('mlw_qmn_options_tab_content', 'mlw_options_preview_tab_content');
?>
