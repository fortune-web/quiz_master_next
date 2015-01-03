<?php
function mlw_options_questions_tab()
{
	echo "<li><a href=\"#tabs-1\">Questions</a></li>";
}

function mlw_options_questions_tab_content()
{
	global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = $_GET["quiz_id"];
	
	//Edit question
	if ( isset($_POST["edit_question"]) && $_POST["edit_question"] == "confirmation")
	{
		//Variables from edit question form
		$edit_question_name = trim(preg_replace('/\s+/',' ', nl2br(htmlspecialchars($_POST["edit_question_name"], ENT_QUOTES))));
		$edit_question_answer_info = $_POST["edit_correct_answer_info"];
		$mlw_edit_question_id = intval($_POST["edit_question_id"]);
		$mlw_edit_question_type = $_POST["edit_question_type"];
		$edit_comments = htmlspecialchars($_POST["edit_comments"], ENT_QUOTES);
		$edit_hint = htmlspecialchars($_POST["edit_hint"], ENT_QUOTES);
		$edit_question_order = intval($_POST["edit_question_order"]);
		$mlw_edit_answer_total = intval($_POST["question_".$mlw_edit_question_id."_answer_total"]);
		$mlw_row_settings = $wpdb->get_row( $wpdb->prepare( "SELECT question_settings FROM " . $wpdb->prefix . "mlw_questions" . " WHERE question_id=%d", $mlw_edit_question_id ) );
		if (is_serialized($mlw_row_settings->question_settings) && is_array(@unserialize($mlw_row_settings->question_settings))) 
		{
			$mlw_settings = @unserialize($mlw_row_settings->question_settings);
		}
		else
		{
			$mlw_settings = array();
			$mlw_settings['required'] = intval($_POST["edit_required"]);
		}
		if ( !isset($mlw_settings['required']))
		{
			$mlw_settings['required'] = intval($_POST["edit_required"]);	
		}
		$mlw_settings['required'] = intval($_POST["edit_required"]);		
		$mlw_settings = serialize($mlw_settings);
		$i = 1;
		$mlw_qmn_new_answer_array = array();
		while ($i <= $mlw_edit_answer_total)
		{
			if ($_POST["edit_answer_".$i] != "")
			{
				$mlw_qmn_correct = 0;
				if (isset($_POST["edit_answer_".$i."_correct"]) && $_POST["edit_answer_".$i."_correct"] == 1)
				{
					$mlw_qmn_correct = 1;
				}
				$mlw_qmn_answer_each = array(htmlspecialchars(stripslashes($_POST["edit_answer_".$i]), ENT_QUOTES), floatval($_POST["edit_answer_".$i."_points"]), $mlw_qmn_correct);
				$mlw_qmn_new_answer_array[] = $mlw_qmn_answer_each;
			}
			$i++;
		}
		$mlw_qmn_new_answer_array = serialize($mlw_qmn_new_answer_array);
		$quiz_id = $_POST["quiz_id"];
		
		$update = "UPDATE " . $wpdb->prefix . "mlw_questions" . " SET question_name='".$edit_question_name."',answer_array='".$mlw_qmn_new_answer_array."', question_answer_info='".$edit_question_answer_info."', comments='".$edit_comments."', hints='".$edit_hint."', question_order='".$edit_question_order."', question_type='".$mlw_edit_question_type."', question_settings='".$mlw_settings."' WHERE question_id=".$mlw_edit_question_id;
		$results = $wpdb->query( $update );
		if ($results != false)
		{
			$mlwQuizMasterNext->alertManager->newAlert('The question has been updated successfully.', 'success');
		
			//Insert Action Into Audit Trail
			global $current_user;
			get_currentuserinfo();
			$table_name = $wpdb->prefix . "mlw_qm_audit_trail";
			$insert = "INSERT INTO " . $table_name .
				"(trail_id, action_user, action, time) " .
				"VALUES (NULL , '" . $current_user->display_name . "' , 'Question Has Been Edited: ".$edit_question_name."' , '" . date("h:i:s A m/d/Y") . "')";
			$results = $wpdb->query( $insert );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert('There has been an error in this action. Please share this with the developer. Error Code: 0004.', 'error');
		}
	}
	//Delete question from quiz
	if ( isset($_POST["delete_question"]) && $_POST["delete_question"] == "confirmation")
	{
		//Variables from delete question form
		$mlw_question_id = intval($_POST["question_id"]);
		$quiz_id = $_POST["quiz_id"];
		
		$update = "UPDATE " . $wpdb->prefix . "mlw_questions" . " SET deleted=1 WHERE question_id=".$mlw_question_id;
		$results = $wpdb->query( $update );
		if ($results != false)
		{
			$mlwQuizMasterNext->alertManager->newAlert('The question has been deleted successfully.', 'success');
		
			//Insert Action Into Audit Trail
			global $current_user;
			get_currentuserinfo();
			$table_name = $wpdb->prefix . "mlw_qm_audit_trail";
			$insert = "INSERT INTO " . $table_name .
				"(trail_id, action_user, action, time) " .
				"VALUES (NULL , '" . $current_user->display_name . "' , 'Question Has Been Deleted: ".$mlw_question_id."' , '" . date("h:i:s A m/d/Y") . "')";
			$results = $wpdb->query( $insert );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert('There has been an error in this action. Please share this with the developer. Error Code: 0005.', 'error');
		}
	}
	
	//Duplicate Questions
	if ( isset($_POST["duplicate_question"]) && $_POST["duplicate_question"] == "confirmation")
	{
		//Variables from delete question form
		$mlw_question_id = intval($_POST["duplicate_question_id"]);
		$quiz_id = $_POST["quiz_id"];
		
		$mlw_original = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."mlw_questions WHERE question_id=%d", $mlw_question_id ), ARRAY_A );
		
		$results = $wpdb->insert( 
						$wpdb->prefix."mlw_questions", 
						array( 
							'quiz_id' => $mlw_original['quiz_id'], 
							'question_name' => $mlw_original['question_name'],
							'answer_array' => $mlw_original['answer_array'], 
							'answer_one' => $mlw_original['answer_one'],
							'answer_one_points' => $mlw_original['answer_one_points'], 
							'answer_two' => $mlw_original['answer_two'],
							'answer_two_points' => $mlw_original['answer_two_points'], 
							'answer_three' => $mlw_original['answer_three'],
							'answer_three_points' => $mlw_original['answer_three_points'], 
							'answer_four' => $mlw_original['answer_four'],
							'answer_four_points' => $mlw_original['answer_four_points'], 
							'answer_five' => $mlw_original['answer_five'],
							'answer_five_points' => $mlw_original['answer_five_points'], 
							'answer_six' => $mlw_original['answer_six'],
							'answer_six_points' => $mlw_original['answer_six_points'], 
							'correct_answer' => $mlw_original['correct_answer'],
							'question_answer_info' => $mlw_original['question_answer_info'], 
							'comments' => $mlw_original['comments'],
							'hints' => $mlw_original['hints'], 
							'question_order' => $mlw_original['question_order'],
							'question_type' => $mlw_original['question_type'], 
							'question_settings' => $mlw_original['question_settings'], 
							'deleted' => $mlw_original['deleted']
						), 
						array( 
							'%d', 
							'%s',
							'%s', 
							'%s',
							'%d', 
							'%s',
							'%d', 
							'%s',
							'%d', 
							'%s',
							'%d', 
							'%s',
							'%d', 
							'%s',
							'%d', 
							'%d',
							'%s',
							'%d',
							'%s',
							'%d',
							'%d',
							'%s',
							'%d'
						) 
					);
		
		if ($results != false)
		{
			$mlwQuizMasterNext->alertManager->newAlert('The question has been duplicated successfully.', 'success');
		
			//Insert Action Into Audit Trail
			global $current_user;
			get_currentuserinfo();
			$table_name = $wpdb->prefix . "mlw_qm_audit_trail";
			$insert = "INSERT INTO " . $table_name .
				"(trail_id, action_user, action, time) " .
				"VALUES (NULL , '" . $current_user->display_name . "' , 'Question Has Been Duplicated: ".$mlw_question_id."' , '" . date("h:i:s A m/d/Y") . "')";
			$results = $wpdb->query( $insert );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert('There has been an error in this action. Please share this with the developer. Error Code: 0019.', 'error');
		}
	}
	
	//Submit new question into database
	if ( isset($_POST["create_question"]) && $_POST["create_question"] == "confirmation")
	{
		//Variables from new question form
		$question_name = trim(preg_replace('/\s+/',' ', nl2br(htmlspecialchars($_POST["question_name"], ENT_QUOTES))));
		$question_answer_info = $_POST["correct_answer_info"];
		$question_type = $_POST["question_type"];
		$comments = htmlspecialchars($_POST["comments"], ENT_QUOTES);
		$hint = htmlspecialchars($_POST["hint"], ENT_QUOTES);
		$new_question_order = intval($_POST["new_question_order"]);
		$mlw_answer_total = intval($_POST["new_question_answer_total"]);
		$mlw_settings = array();
		$mlw_settings['required'] = intval($_POST["required"]);
		$mlw_settings = serialize($mlw_settings);
		$i = 1;
		$mlw_qmn_new_answer_array = array();
		while ($i <= $mlw_answer_total)
		{
			if ($_POST["answer_".$i] != "")
			{
				$mlw_qmn_correct = 0;
				if (isset($_POST["answer_".$i."_correct"]) && $_POST["answer_".$i."_correct"] == 1)
				{
					$mlw_qmn_correct = 1;
				}
				$mlw_qmn_answer_each = array(htmlspecialchars(stripslashes($_POST["answer_".$i]), ENT_QUOTES), floatval($_POST["answer_".$i."_points"]), $mlw_qmn_correct);
				$mlw_qmn_new_answer_array[] = $mlw_qmn_answer_each;
			}
			$i++;
		}
		$mlw_qmn_new_answer_array = serialize($mlw_qmn_new_answer_array);
		$quiz_id = $_POST["quiz_id"];
		$table_name = $wpdb->prefix . "mlw_questions";
		$insert = "INSERT INTO " . $table_name .
			" (question_id, quiz_id, question_name, answer_array, question_answer_info, comments, hints, question_order, question_type, question_settings, deleted) VALUES (NULL , ".$quiz_id.", '" . $question_name . "' , '".$mlw_qmn_new_answer_array."', '".$question_answer_info."', '".$comments."', '".$hint."', ".$new_question_order.", '".$question_type."', '".$mlw_settings."',  0)";
		$results = $wpdb->query( $insert );
		if ($results != false)
		{
			$mlwQuizMasterNext->alertManager->newAlert('The question has been created successfully.', 'success');
		
			//Insert Action Into Audit Trail
			global $current_user;
			get_currentuserinfo();
			$table_name = $wpdb->prefix . "mlw_qm_audit_trail";
			$insert = "INSERT INTO " . $table_name .
				"(trail_id, action_user, action, time) " .
				"VALUES (NULL , '" . $current_user->display_name . "' , 'Question Has Been Added: ".$question_name."' , '" . date("h:i:s A m/d/Y") . "')";
			$results = $wpdb->query( $insert );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert('There has been an error in this action. Please share this with the developer. Error Code: 0006.', 'error');
		}
	}
	
	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $_GET["quiz_id"]));
	}
	$mlw_qmn_table_limit = 10;
	$mlw_qmn_question_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(question_id) FROM " . $wpdb->prefix . "mlw_questions WHERE quiz_id=%d AND deleted='0'", $quiz_id ) );
	
	if( isset($_GET{'mlw_question_page'} ) )
	{
	   $mlw_qmn_question_page = $_GET{'mlw_question_page'} + 1;
	   $mlw_qmn_question_begin = $mlw_qmn_table_limit * $mlw_qmn_question_page ;
	}
	else
	{
	   $mlw_qmn_question_page = 0;
	   $mlw_qmn_question_begin = 0;
	}
	$mlw_qmn_question_left = $mlw_qmn_question_count - ($mlw_qmn_question_page * $mlw_qmn_table_limit);
	
	$mlw_question_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "mlw_questions WHERE quiz_id=%d AND deleted='0' 
		ORDER BY question_order ASC LIMIT %d, %d", $quiz_id, $mlw_qmn_question_begin, $mlw_qmn_table_limit ) );
		
	//Load and prepare answer arrays
	$mlw_qmn_answer_arrays = array();
	foreach($mlw_question_data as $mlw_question_info) {
		if (is_serialized($mlw_question_info->answer_array) && is_array(@unserialize($mlw_question_info->answer_array))) 
		{
			$mlw_qmn_answer_array_each = @unserialize($mlw_question_info->answer_array);
			$mlw_qmn_answer_arrays[$mlw_question_info->question_id] = $mlw_qmn_answer_array_each;
		}
		else
		{
			$mlw_answer_array_correct = array(0, 0, 0, 0, 0, 0);
			$mlw_answer_array_correct[$mlw_question_info->correct_answer-1] = 1;
			$mlw_qmn_answer_arrays[$mlw_question_info->question_id] = array(
				array($mlw_question_info->answer_one, $mlw_question_info->answer_one_points, $mlw_answer_array_correct[0]),
				array($mlw_question_info->answer_two, $mlw_question_info->answer_two_points, $mlw_answer_array_correct[1]),
				array($mlw_question_info->answer_three, $mlw_question_info->answer_three_points, $mlw_answer_array_correct[2]),
				array($mlw_question_info->answer_four, $mlw_question_info->answer_four_points, $mlw_answer_array_correct[3]),
				array($mlw_question_info->answer_five, $mlw_question_info->answer_five_points, $mlw_answer_array_correct[4]),
				array($mlw_question_info->answer_six, $mlw_question_info->answer_six_points, $mlw_answer_array_correct[5]));
		}
	}
	$is_new_quiz = $wpdb->num_rows;
	?>
	<div id="tabs-1" class="mlw_tab_content">
		<script>
			jQuery(function() {
				jQuery('#new_question_dialog').dialog({
					autoOpen: false,
					show: 'blind',
					width:800,
					hide: 'explode',
					buttons: {
					Cancel: function() {
						jQuery(this).dialog('close');
						}
					}
				});
			
				jQuery('#new_question_button').click(function() {
					jQuery('#new_question_dialog').dialog('open');
					document.getElementById("question_name").focus();
					return false;
			}	);
				jQuery('#new_question_button_two').click(function() {
					jQuery('#new_question_dialog').dialog('open');
					document.getElementById("question_name").focus();
					return false;
			}	);
			});
			function deleteQuestion(id){
				jQuery("#delete_dialog").dialog({
					autoOpen: false,
					show: 'blind',
					hide: 'explode',
					buttons: {
					Cancel: function() {
						jQuery(this).dialog('close');
						}
					}
				});
				jQuery("#delete_dialog").dialog('open');
				var idText = document.getElementById("delete_question_id");
				var idHidden = document.getElementById("question_id");
				idText.innerHTML = id;
				idHidden.value = id;		
			};
			function duplicateQuestion(id){
				jQuery("#duplicate_dialog").dialog({
					autoOpen: false,
					show: 'blind',
					hide: 'explode',
					buttons: {
					Cancel: function() {
						jQuery(this).dialog('close');
						}
					}
				});
				jQuery("#duplicate_dialog").dialog('open');
				var idHidden = document.getElementById("duplicate_question_id");
				idHidden.value = id;		
			};
			function editQuestion(id){
				jQuery("#edit_question_dialog_"+id).dialog({
					autoOpen: false,
					show: 'blind',
					width:800,
					hide: 'explode',
					buttons: {
					Cancel: function() {
						jQuery(this).dialog('close');
						}
					}
				});
				jQuery("#edit_question_dialog_"+id).dialog('open');
			};
			function mlw_add_new_question(id)
			{
				var total_answers = parseFloat(document.getElementById("question_"+id+"_answer_total").value);
				total_answers = total_answers + 1;
				document.getElementById("question_"+id+"_answer_total").value = total_answers;
				jQuery("#question_"+id+"_answers").append("<tr valign='top'><td><span style='font-weight:bold;'>Answer "+total_answers+"</span></td><td><input type='text' name='edit_answer_"+total_answers+"' id='edit_answer_"+total_answers+"' style='border-color:#000000;color:#3300CC;cursor:hand;width: 250px;'/></td><td><input type='text' name='edit_answer_"+total_answers+"_points' id='edit_answer_"+total_answers+"_points' value=0 style='border-color:#000000;color:#3300CC; cursor:hand;'/></td><td><input type='checkbox' id='edit_answer_"+total_answers+"_correct' name='edit_answer_"+total_answers+"_correct' value=1 /></td></tr>");
			}
			function mlw_add_answer_to_new_question()
			{
				var total_answers = parseFloat(document.getElementById("new_question_answer_total").value);
				total_answers = total_answers + 1;
				document.getElementById("new_question_answer_total").value = total_answers;
				jQuery("#new_question_answers").append("<tr valign='top'><td><span style='font-weight:bold;'>Answer "+total_answers+"</span></td><td><input type='text' name='answer_"+total_answers+"' id='answer_"+total_answers+"' style='border-color:#000000;color:#3300CC;cursor:hand;width: 250px;'/></td><td><input type='text' name='answer_"+total_answers+"_points' id='answer_"+total_answers+"_points' value=0 style='border-color:#000000;color:#3300CC; cursor:hand;'/></td><td><input type='checkbox' id='answer_"+total_answers+"_correct' name='answer_"+total_answers+"_correct' value=1 /></td></tr>");
			}
		</script>
		<style>
			.linkOptions
			{
				color: #0074a2 !important;
				font-size: 14px !important;
			}
			.linkDelete
			{
				color: red !important;
				font-size: 14px !important;
			}
			.linkOptions:hover,
			.linkDelete:hover
			{
				background-color: black;
			}
		</style>
		<button class="button" id="new_question_button_two">Add Question</button>
		<br />
		<?php
		$question_list = "";
		$display = "";
		$alternate = "";
		foreach($mlw_question_data as $mlw_question_info) {
			if (is_serialized($mlw_question_info->question_settings) && is_array(@unserialize($mlw_question_info->question_settings))) 
			{
				$mlw_question_settings = @unserialize($mlw_question_info->question_settings);
			}
			else
			{
				$mlw_question_settings = array();
				$mlw_question_settings['required'] = 1;
			}
			$mlw_question_type_text = "";
			switch ($mlw_question_info->question_type) {
				case 0:
					$mlw_question_type_text = "Multiple Choice";
					break;
				case 1:
					$mlw_question_type_text = "Horizontal Multiple Choice";
					break;
				case 2:
					$mlw_question_type_text = "Drop Down";
					break;
				case 3:
					$mlw_question_type_text = "Small Open Answer";
					break;
				case 4:
					$mlw_question_type_text = "Multiple Response";
					break;
				case 5:
					$mlw_question_type_text = "Large Open Answer";
					break;
				case 6:
					$mlw_question_type_text = "Text Block";
					break;
				case 7:
					$mlw_question_type_text = "Number";
					break;
				case 8:
					$mlw_question_type_text = "Accept";
					break;
				case 9:
					$mlw_question_type_text = "Captcha";
					break;
				case 10:
					$mlw_question_type_text = "Horizontal Multiple Response";
					break;
				default:
					$mlw_question_type_text = "Error Code ";
			}
			if($alternate) $alternate = "";
			else $alternate = " class=\"alternate\"";
			$question_list .= "<tr{$alternate}>";
			$question_list .= "<td><span style='font-size:16px;'>" . $mlw_question_info->question_order . "</span></td>";
			$question_list .= "<td><span style='font-size:16px;'>" . $mlw_question_type_text . "</span></td>";
			$question_list .= "<td class='post-title column-title'><span style='font-size:16px;'>" . $mlw_question_info->question_name ."</span><div class='row-actions'><a class='linkOptions' onclick=\"editQuestion('".$mlw_question_info->question_id."')\" href='#'>Edit</a> | <a class='linkOptions' onclick=\"duplicateQuestion('".$mlw_question_info->question_id."')\" href='#'>Duplicate</a>| <a class='linkDelete' onclick=\"deleteQuestion('".$mlw_question_info->question_id."')\" href='#'>Delete</a></div></td>";
			$question_list .= "</tr>";
			
			
			$mlw_question_answer_array = $mlw_qmn_answer_arrays[$mlw_question_info->question_id];
			?>
			<div id="edit_question_dialog_<?php echo $mlw_question_info->question_id; ?>" title="Edit Question" style="display:none;">
			<?php
			echo "<form action='' method='post'>";
			echo "<input type='hidden' name='edit_question' value='confirmation' />";
			echo "<input type='hidden' id='edit_question_id' name='edit_question_id' value='".$mlw_question_info->question_id."' />";
			echo "<input type='hidden' name='quiz_id' value='".$quiz_id."' />";
			?>
			<table class="wide" style="text-align: left; white-space: nowrap;" id="question_<?php echo $mlw_question_info->question_id; ?>_answers" name="question_<?php echo $mlw_question_info->question_id; ?>_answers">
			<tr>
			<td><span style='font-weight:bold;'>Question</span></td>
			<td colspan="3">
				<textarea name="edit_question_name" id="edit_question_name" style="width: 500px; height: 150px;"><?php echo htmlspecialchars_decode($mlw_question_info->question_name, ENT_QUOTES); ?></textarea>
			</td>
			</tr>
			<tr valign="top">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>
			<tr valign="top">
			<td>&nbsp;</td>
			<td><span style='font-weight:bold;'>Answers</span></td>
			<td><span style='font-weight:bold;'>Points Worth</span></td>
			<td><span style='font-weight:bold;'>Correct Answer</span></td>
			</tr>
			<?php
			$mlw_answer_total = 0;
			foreach($mlw_question_answer_array as $mlw_question_answer_each)
			{
				$mlw_answer_total = $mlw_answer_total + 1;
				?>
				<tr valign="top">
				<td><span style='font-weight:bold;'>Answer <?php echo $mlw_answer_total; ?></span></td>
				<td>
				<input type="text" name="edit_answer_<?php echo $mlw_answer_total; ?>" id="edit_answer_<?php echo $mlw_answer_total; ?>" value="<?php echo esc_attr(htmlspecialchars_decode($mlw_question_answer_each[0], ENT_QUOTES)); ?>" style="border-color:#000000;
					color:#3300CC; 
					cursor:hand;
					width: 250px;"/>
				</td>
				<td>
				<input type="text" name="edit_answer_<?php echo $mlw_answer_total; ?>_points" id="edit_answer_<?php echo $mlw_answer_total; ?>_points" value="<?php echo $mlw_question_answer_each[1]; ?>" style="border-color:#000000;
					color:#3300CC; 
					cursor:hand;"/>
				</td>
				<td><input type="checkbox" id="edit_answer_<?php echo $mlw_answer_total; ?>_correct" name="edit_answer_<?php echo $mlw_answer_total; ?>_correct" <?php if ($mlw_question_answer_each[2] == 1) { echo 'checked="checked"'; } ?> value=1 /></td>
				</tr>			
				<?php
			}
			?>
			</table>
			<a href="#" class="button" id="new_answer_button" onclick="mlw_add_new_question(<?php echo $mlw_question_info->question_id; ?>);">Add New Answer!</a>
			<br />
			<br />
			<table class="wide" style="text-align: left; white-space: nowrap;">
			<tr>
				<td><span style='font-weight:bold;'>Correct Answer Info:</span></td>
				<td colspan="3"><input type="text" name="edit_correct_answer_info" id="edit_correct_answer_info" style="border-color:#000000;
				color:#3300CC; 
				cursor:hand;
				width:550px;" value="<?php echo esc_attr(htmlspecialchars_decode($mlw_question_info->question_answer_info, ENT_QUOTES)); ?>"/></td>
			</tr>
			<tr valign="top">
			<td><span style='font-weight:bold;'>Hint</span></td>
			<td colspan="3">
			<input type="text" name="edit_hint" id="edit_hint" style="border-color:#000000;
				color:#3300CC; 
				cursor:hand;
				width:550px;" value="<?php echo htmlspecialchars_decode($mlw_question_info->hints, ENT_QUOTES); ?>"/>
			</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr valign="top">
			<td><span style='font-weight:bold;'>Question Type</span></td>
			<td colspan="3">
				<select name="edit_question_type">
					<option value="0" <?php if ($mlw_question_info->question_type == 0) { echo 'selected="selected"'; } ?>>Normal Multiple Choice (Vertical Radio)</option>
					<option value="1" <?php if ($mlw_question_info->question_type == 1) { echo 'selected="selected"'; } ?>>Horizontal Multiple Choice (Horizontal Radio)</option>
					<option value="2" <?php if ($mlw_question_info->question_type == 2) { echo 'selected="selected"'; } ?>>Drop Down (Select)</option>
					<option value="3" <?php if ($mlw_question_info->question_type == 3) { echo 'selected="selected"'; } ?>>Open Answer (Text Input)</option>
					<option value="5" <?php if ($mlw_question_info->question_type == 5) { echo 'selected="selected"'; } ?>>Open Answer (Large Text Input)</option>
					<option value="4" <?php if ($mlw_question_info->question_type == 4) { echo 'selected="selected"'; } ?>>Multiple Response (Checkbox)</option>
					<option value="10" <?php if ($mlw_question_info->question_type == 10) { echo 'selected="selected"'; } ?>>Horizontal Multiple Response (Checkbox)</option>
					<option value="6" <?php if ($mlw_question_info->question_type == 6) { echo 'selected="selected"'; } ?>>Text Block</option>
					<option value="7" <?php if ($mlw_question_info->question_type == 7) { echo 'selected="selected"'; } ?>>Number</option>
					<option value="8" <?php if ($mlw_question_info->question_type == 8) { echo 'selected="selected"'; } ?>>Accept</option>
					<option value="9" <?php if ($mlw_question_info->question_type == 9) { echo 'selected="selected"'; } ?>>Captcha</option>
				</select>
			</div></td>
			</tr>
			<tr valign="top">
			<td><span style='font-weight:bold;'>Comment Field</span></td>
			<td colspan="3">
				<input type="radio" id="<?php echo $mlw_question_info->question_id; ?>_editCommentRadio1" name="edit_comments" value=0 <?php if ($mlw_question_info->comments == 0) { echo 'checked="checked"'; } ?>/><label for="<?php echo $mlw_question_info->question_id; ?>_editCommentRadio1">Small Text Field</label>
				<input type="radio" id="<?php echo $mlw_question_info->question_id; ?>_editCommentRadio3" name="edit_comments" value=2 <?php if ($mlw_question_info->comments == 2) { echo 'checked="checked"'; } ?>/><label for="<?php echo $mlw_question_info->question_id; ?>_editCommentRadio3">Large Text Field</label>
				<input type="radio" id="<?php echo $mlw_question_info->question_id; ?>_editCommentRadio2" name="edit_comments" value=1 <?php if ($mlw_question_info->comments == 1) { echo 'checked="checked"'; } ?>/><label for="<?php echo $mlw_question_info->question_id; ?>_editCommentRadio2">None</label>
			</td>
			</tr>
			<tr valign="top">
			<td><span style='font-weight:bold;'>Question Order</span></td>
			<td>
			<input type="number" step="1" min="1" name="edit_question_order" value="<?php echo $mlw_question_info->question_order; ?>" id="edit_question_order" style="border-color:#000000;
				color:#3300CC; 
				cursor:hand;"/>
			</td>
			</tr>
			<tr valign="top">
			<td><span style='font-weight:bold;'>Required?</span></td>
			<td colspan="3">
				<select name="edit_required">
					<option value="0" <?php if ($mlw_question_settings['required'] == 0) { echo 'selected="selected"'; } ?>>Yes</option>
					<option value="1" <?php if ($mlw_question_settings['required'] == 1) { echo 'selected="selected"'; } ?>>No</option>
				</select>
			</div></td>
			</tr>
			</table>
			<p> *Required currently only works on open answer, number, accept, and captcha question types</p>
			<input type="hidden" name="question_<?php echo $mlw_question_info->question_id; ?>_answer_total" id="question_<?php echo $mlw_question_info->question_id; ?>_answer_total" value="<?php echo $mlw_answer_total; ?>" />
			<p class='submit'><input type='submit' class='button-primary' value='Edit Question' /></p>
			</form>
			</div>	
			
			<?php
		}
		
		if( $mlw_qmn_question_page > 0 )
		{
			$mlw_qmn_previous_page = $mlw_qmn_question_page - 2;
			$display .= "<a id=\"prev_page\" href=\"?page=mlw_quiz_options&&mlw_question_page=$mlw_qmn_previous_page&&quiz_id=$quiz_id\">Previous 10 Questions</a>";
			if( $mlw_qmn_question_left > $mlw_qmn_table_limit )
			{
				$display .= "<a id=\"next_page\" href=\"?page=mlw_quiz_options&&mlw_question_page=$mlw_qmn_question_page&&quiz_id=$quiz_id\">Next 10 Questions</a>";
			}
		}
		else if( $mlw_qmn_question_page == 0 )
		{
		   if( $mlw_qmn_question_left > $mlw_qmn_table_limit )
		   {
				$display .= "<a id=\"next_page\" href=\"?page=mlw_quiz_options&&mlw_question_page=$mlw_qmn_question_page&&quiz_id=$quiz_id\">Next 10 Questions</a>";
		   }
		}
		else if( $mlw_qmn_question_left < $mlw_qmn_table_limit )
		{
		   $mlw_qmn_previous_page = $mlw_qmn_question_page - 2;
		   $display .= "<a id=\"prev_page\" href=\"?page=mlw_quiz_options&&mlw_question_page=$mlw_qmn_previous_page&&quiz_id=$quiz_id\">Previous 10 Questions</a>";
		}
		$display .= "<table class=\"widefat\">";
			$display .= "<thead><tr>
				<th>Question Order</th>
				<th>Question Type</th>
				<th>Question</th>
			</tr></thead>";
			$display .= "<tbody id=\"the-list\">{$question_list}</tbody>";
			$display .= "<tfoot><tr>
				<th>Question Order</th>
				<th>Question Type</th>
				<th>Question</th>
			</tr></tfoot>";
			$display .= "</table>";
		echo $display;
		?>
		<button class="button" id="new_question_button">Add Question</button>
		<div id="new_question_dialog" title="Create New Question" style="display:none;">
		
		<?php
		echo "<form action='' method='post'>";
		echo "<input type='hidden' name='create_question' value='confirmation' />";
		echo "<input type='hidden' name='quiz_id' value='".$quiz_id."' />";
		?>		
		<table class="wide" style="text-align: left; white-space: nowrap;" id="new_question_answers" name="new_question_answers">
		<tr>
		<td><span style='font-weight:bold;'>Question</span></td>
		<td colspan="3">
			<textarea name="question_name" id="question_name" style="width: 500px; height: 150px;"></textarea>
		</td>
		</tr>
		<tr valign="top">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
		<tr valign="top">
		<td>&nbsp;</td>
		<td><span style='font-weight:bold;'>Answers</span></td>
		<td><span style='font-weight:bold;'>Points Worth</span></td>
		<td><span style='font-weight:bold;'>Correct Answer</span></td>
		</tr>
		<?php
		$mlw_answer_total = 0;
		$mlw_answer_total = $mlw_answer_total + 1;
		?>
		<tr valign="top">
		<td><span style='font-weight:bold;'>Answer <?php echo $mlw_answer_total; ?></span></td>
		<td>
		<input type="text" name="answer_<?php echo $mlw_answer_total; ?>" id="answer_<?php echo $mlw_answer_total; ?>" value="" style="border-color:#000000;
			color:#3300CC; 
			cursor:hand;
			width: 250px;"/>
		</td>
		<td>
		<input type="text" name="answer_<?php echo $mlw_answer_total; ?>_points" id="answer_<?php echo $mlw_answer_total; ?>_points" value=0 style="border-color:#000000;
			color:#3300CC; 
			cursor:hand;"/>
		</td>
		<td><input type="checkbox" id="answer_<?php echo $mlw_answer_total; ?>_correct" name="answer_<?php echo $mlw_answer_total; ?>_correct" checked="checked" value=1 /></td>
		</tr>
		</table>
		<a href="#" class="button" id="new_answer_button" onclick="mlw_add_answer_to_new_question();">Add New Answer!</a>
		<br />
		<br />
		<table class="wide" style="text-align: left; white-space: nowrap;">
		<tr>
			<td><span style='font-weight:bold;'>Correct Answer Info</span></td>
			<td colspan="3"><input type="text" name="correct_answer_info" value="" id="correct_answer_info" style="border-color:#000000;
			color:#3300CC; 
			cursor:hand;
			width:550px;"/></td>
		</tr>
		<tr valign="top">
		<td><span style='font-weight:bold;'>Hint</span></td>
		<td colspan="3">
		<input type="text" name="hint" value="" id="hint" style="border-color:#000000;
			color:#3300CC; 
			cursor:hand;
			width:550px;"/>
		</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr valign="top">
		<td><span style='font-weight:bold;'>Question Type</span></td>
		<td colspan="3">
			<select name="question_type">
				<option value="0" selected="selected">Normal Multiple Choice (Vertical Radio)</option>
				<option value="1">Horizontal Multiple Choice (Horizontal Radio)</option>
				<option value="2">Drop Down (Select)</option>
				<option value="3">Open Answer (Text Input)</option>
				<option value="5">Open Answer (Large Text Input)</option>
				<option value="4">Multiple Response (Checkbox)</option>
				<option value="10">Horizontal Multiple Response (Checkbox)</option>
				<option value="6">Text Block</option>
				<option value="7">Number</option>
				<option value="8">Accept</option>
				<option value="9">Captcha</option>
			</select>
		</div></td>
		</tr>
		<tr valign="top">
		<td><span style='font-weight:bold;'>Comment Field</span></td>
		<td colspan="3"><div id="comments">
			<input type="radio" id="commentsRadio1" name="comments" value=0 /><label for="commentsRadio1">Small Text Field</label>
			<input type="radio" id="commentsRadio3" name="comments" value=2 /><label for="commentsRadio3">Large Text Field</label>
			<input type="radio" id="commentsRadio2" name="comments" checked="checked" value=1 /><label for="commentsRadio2">None</label>
		</div></td>
		</tr>
		<tr valign="top">
		<td><span style='font-weight:bold;'>Question Order</span></td>
		<td>
		<input type="number" step="1" min="1" name="new_question_order" value="<?php echo $mlw_qmn_question_count+1; ?>" id="new_question_order" style="border-color:#000000;
			color:#3300CC; 
			cursor:hand;"/>
		</td>
		</tr>
		<tr valign="top">
		<td><span style='font-weight:bold;'>Required?</span></td>
		<td colspan="3">
			<select name="required">
				<option value="0" selected="selected">Yes</option>
				<option value="1">No</option>
			</select>
		</div></td>
		</tr>
		</table>
		<p> *Required currently only works on open answer, number, accept, and captcha question types</p>
		<input type="hidden" name="new_question_answer_total" id="new_question_answer_total" value="<?php echo $mlw_answer_total; ?>" />
		<?php
		echo "<p class='submit'><input type='submit' class='button-primary' value='Create Question' /></p>";
		echo "</form>";
		?>
		</div>
		<!--Dialogs-->
		<div id="delete_dialog" title="Delete Question?" style="display:none;">
			<h3><b>Are you sure you want to delete Question <span id="delete_question_id"></span>?</b></h3>
			<?php
			echo "<form action='' method='post'>";
			echo "<input type='hidden' name='delete_question' value='confirmation' />";
			echo "<input type='hidden' id='question_id' name='question_id' value='' />";
			echo "<input type='hidden' name='quiz_id' value='".$quiz_id."' />";
			echo "<p class='submit'><input type='submit' class='button-primary' value='Delete Question' /></p>";
			echo "</form>";	
			?>
		</div>
		
		<div id="duplicate_dialog" title="Duplicate Question?" style="display:none;">
			<h3><b>Are you sure you want to duplicate this Question?</b></h3>
			<?php
			echo "<form action='' method='post'>";
			echo "<input type='hidden' name='duplicate_question' value='confirmation' />";
			echo "<input type='hidden' id='duplicate_question_id' name='duplicate_question_id' value='' />";
			echo "<input type='hidden' name='quiz_id' value='".$quiz_id."' />";
			echo "<p class='submit'><input type='submit' class='button-primary' value='Duplicate Question' /></p>";
			echo "</form>";	
			?>
		</div>
	</div>
	<?php
}
?>
