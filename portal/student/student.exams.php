<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<script src="<?=SYSTEM_URL;?>/javascript/multifile.js"></script>
<script type="text/javascript">
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Student | Exams";
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
$(document).ready(function() {
	//Load TinyMCE	
	tinymce.init({		
		selector: 'textarea.tinymce',
		height: 200,
		theme: 'modern',
		menubar: false,	
		plugins: [
			'advlist autolink lists link image charmap print preview anchor textcolor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table contextmenu paste code help wordcount'
		],
		toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | help',
		content_css: "<?=SYSTEM_URL;?>/styles/tinymce.editor.css"
	});
});
//-->
$('.table').dataTable( {
  "pageLength": 1
} );
</script>
<?php 
// attempt marking the paper
if( isset($_POST['mark']) ){
//1. get all the questions IDs
$total_qs = $_POST['totalsquestions'];
//echo '<script>window.alert("'.$total_qs.'");</script>';
$looper = 0;
$question_IDs = array();
while( $looper < $total_qs ){
    $post_name = 'QuestionID'.$looper;
    array_push( $question_IDs, $_POST[$post_name] );
$looper++;
}
//2. get answers for each question based on question IDs
$looper = 0;
$total_score = 0;
$manual_marking = array();
foreach( $question_IDs as $q ):
	$post_name = 'answers'.$q;
	//if closed question
	if (is_numeric($_POST[$post_name])) {
	$total_score = $total_score + $_POST[$post_name];
	}else{
$m_element = array('Question'=>$q, 'StudentAnswer'=>$_POST[$post_name]);
array_push($manual_marking, $m_element);
	}
endforeach;
//get exam details and student details
$ExamID = $_POST['ExamID'];
$StudentID = $_POST['StudentID'];
$ExamState = "Completed";
$unmarked = json_encode($manual_marking);

//send exam to db
$params = array($StudentID, $ExamID, $total_score, $ExamState, $unmarked);
recordScore($params);

echo '<div class="alert alert-success">
<strong>Done</strong> You have completed your paper. Provisional(Without Open ended questions) Score is: <b>'.$total_score.'</b>
</div>';
//print_r($unmarked);

exit;
}
?>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Upcoming Exams</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
      <!--Begin Forms-->
      <?php
			require_once("$class_dir/class.validator.php3");
			$_SESSION['STUD'] = $student['StudentID'];
	  if(!empty(($student['StudentID']))){		  
			//call form to open exam questions window
			if(isset($_POST['start'])){
            $ExamID = $_POST['ExamID'];
                //RESET STATUS OF THIS EXAM FOR THIS STUDENT
                changeExamState($ExamID, $student['StudentID'], 'STARTED');
                Start_exam($ExamID, $student['StudentID']);
			}else{
				if(!empty(getStudentExams($student['StudentID']))){
			foreach(getStudentExams($student['StudentID']) as $paper):
		  ?>
		  <h2 style="color:#0085af; font-size:22px;"><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamName'];  ?></h2>
		  <!-- <h3>This exam requires your attention</h3> -->
		  <table width="100%" class="display table table-striped table-bordered table-hover">
		  <thead>
		  <tr>
            <th>ExamID</th>
            <th>Exam Course</th>
            <th>Exam Unit</th>
            <th>Exam Date</th>
            <th>Exam Deadline</th>
            <th>Exam Duration</th>
            <th>Instructions</th>
            <?php if($paper['Status'] != 'PENDING' && $paper['Status'] != 'OPEN'){ ?>
            <th>Score</th>
            <?php } ?>
		  </tr>
		  </thead>
		  <tbody>
			<tr>
			<td><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamID']; ?></td>
			<td><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamCourse']; ?></td>
			<td><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamUnit']; ?></td>
			<td><?php echo dateFixedFromat(getStudentExamsDetails($paper['ExamID'])[0]['ExamDate']); ?></td>
			<td><?php echo dateFixedFromat(getStudentExamsDetails($paper['ExamID'])[0]['ExamDeadline']); ?></td>
			<td><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamDuration'].' Minutes'; ?></td>
			<td><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamInstructions']; ?></td>
            <?php if($paper['Status'] != 'PENDING' && $paper['Status'] != 'OPEN'){ ?>
            <td><?php echo $paper['ExamScore']; ?></td>
            <?php } ?>
			</tr>
		  </tbody>
		  </table>
			<div id="hideMsg"><?php if($paper['Status'] == "OPEN"){ ?>
      <h3>INSTRUCTIONS</h3>
      <p>make sure you are not distructed for at least <b><?php echo getStudentExamsDetails($paper['ExamID'])[0]['ExamDuration'].' Minutes'; ?></b> and that you are ready for this paper. Once you are good with this, just click on <b>Start Exams</b></p>
      <p><b>Do not go back</b> once you click on <b> Start Exam</b>, If you do, you will be automatically assigned <b>Score Zero(0) for this paper.</b></p>
      <p>A <b>timer</b> will be started the moment you click on <b>START EXAM</b> and your answers will be submitted automatically after your time is up even if you have not finished.</p>
      <p>Make sure you navigate through all the <b>exam pages</b> before clicking on <b>Send for Marking</b></p>
			<form action ="" method="post">
			<input type="text" name="ExamID" value ="<?php echo $paper['ExamID']; ?>" hidden="hidden"/>
			<input type="submit" class ="btn btn-primary" name="start" value="Start Exam" />
			</form>
			<?php }else{
				if($paper['Status'] == "Completed" || $paper['Status'] == "STARTED"){
					echo '<input type="submit" class="btn btn-primary" value="Completed" disabled="disabled"/>';
				}else{
				 ?><input type="submit" class="btn btn-primary" value="Start Exam" disabled="disabled"/><?php }} ?></div>
          <?php
					endforeach;
				}else{
					echo '<p>There are currently no active exams for you.</p>';
				}
				}
	  }else{
		  echo '<p>There are currently no active exams for you.</p>';
	  }
      ?>
      <!--End Forms-->
		</div>
  </div>
</div>
<?php
function Start_exam($ExamID, $StudentID){
    Global $conn;
    global $to_preview;
    $to_preview['Question'] = array();
    $to_preview['Question']['Options'] = array();
    //get questions for this paper
$resQuestions = getExamQuestions($ExamID);			
if(db_num_rows($resQuestions)>0){	
    $loop = 0;			
    while($q = db_fetch_array($resQuestions)){
    array_push($to_preview['Question'], $q);
    }
}
if(!empty($to_preview)){
    ?>
<style>
.exam-overlay {		    
    position: fixed;
	width: 100%;
	height: 100%;
	top: 0;
    left: 0;
	padding: 30px;
    background-color: #FFF;
    overflow-x: hidden;
    transition: 0.5s;
    z-index: 99999;
}
</style>
<div class="exam-overlay" id="page-wrap">
<h1><?php echo getExamName($to_preview['Question'][0]['ExamID']); ?></h1>
<div>
<h2 style="color:red;" id="timer"></h1>
<h5 style="color:red;" id="notifier"></h5>
</div>
<h3><?php echo "Instructions"; ?></h3>
<span><em>Take note of the instructions below: You have been added <b>2 minutes</b> to read instructions</em></span><br>
<ul>
<li>
<?php echo getExamInstructions($to_preview['Question'][0]['ExamID']); ?></li>
<li>
<?php echo "You will have exactly ".getExamDuration($to_preview['Question'][0]['ExamID'])." minutes to complete this exam"; ?></li>
<li>
<h2>You are not allowed to go back.</h2>
</li>
<li>
DO NOT refresh this page until you are done.
</li>
<li>
Make sure you have completed ALL questions on ALL pages before you Click on <b>Send for Marking</b> at the bottom of this PAGE
</li>
</ul>
<form method="post" id="exam" action="">
<table width="100%" class="display table table-striped table-bordered table-hover" id="questions">
		  <thead>
		  <tr>
		  <th></th>
		  </tr>
		  </thead>
		  <tbody>
<?php
//print_r($to_preview['Question'][0]);
$count_open = 0;
$loop_questions = 0;
$all_questions = count($to_preview['Question']);
foreach($to_preview['Question'] as $single_question):
if(!empty(decode($single_question['Question']))){
?>
<tr>
<td>
    <h3><?php echo decode($single_question['Question']); ?></h3>
		<input type="text" name ="StudentID" value ="<?php echo $StudentID; ?>" hidden ="hidden"/>
		<input type="text" name ="ExamID" value ="<?php echo $ExamID; ?>" hidden ="hidden"/>
    <input type="text" name ="QuestionID<?php echo $loop_questions;?>" value ="<?php echo $single_question['QuestionID']; ?>" hidden ="hidden"/>
<?php 
   //question options 
   if($single_question['QuestionType'] != "Closed"){
		 $count_open ++;
		 ?>
    <textarea name ="answers<?php echo $single_question['QuestionID'];?>" class="form-control tinymce" value =""></textarea>
<?php   }else{
 if(db_num_rows(db_query(sprintf("SELECT * FROM `".DB_PREFIX."exam_question_options` WHERE `disabledFlag` = %d AND `deletedFlag` = %d AND QuestionID = %d", 0, 0, $single_question['QuestionID']),DB_NAME,$conn))>0){
     $options_list = array();
     $options_result = db_query(sprintf("SELECT * FROM `".DB_PREFIX."exam_question_options` WHERE `disabledFlag` = %d AND `deletedFlag` = %d AND QuestionID = %d", 0, 0, $single_question['QuestionID']),DB_NAME,$conn);
     while($row = db_fetch_array($options_result)){
			 $value = 0;
			 if($row['IsAnswer'] == 1){
				 $value = $single_question['Credits'];
			 }
?>
    <div>
        <input type="radio" name="answers<?php echo $single_question['QuestionID']; ?>" id="question-1-answers-A" value="<?php echo $value; ?>" onclick="UpdateScore()" />
        <label for="question-1-answers-A"><?php echo $row['OptionIdentity'].") ".$row['OptionName'];?></label>
    </div>
<?php
  }
}
} //end question type else
$loop_questions ++;
}
echo "<br><br>
</td></tr>";
endforeach;
    ?>
	</tbody>
</table>
<script type="text/javascript">
(function () {
  function display( notifier, str ) {
    document.getElementById(notifier).innerHTML = str;
  }
  function act(){
    $('#exam :not([type=submit])').prop('hidden',true);
  }
  function toMinuteAndSecond( x ) {
    // return Math.floor(x/60) + ":" + (x=x%60 < 10 ? 0 : x);
    var milliseconds = x;
    var hours = milliseconds / (1000*60*60);
    var absoluteHours = Math.floor(hours);
    var h = absoluteHours > 9 ? absoluteHours : '0' + absoluteHours;
    //Get remainder from hours and convert to minutes
    var minutes = (hours - absoluteHours) * 60;
    var absoluteMinutes = Math.floor(minutes);
    var m = absoluteMinutes > 9 ? absoluteMinutes : '0' +  absoluteMinutes;
    //Get remainder from minutes and convert to seconds
    var seconds = (minutes - absoluteMinutes) * 60;
    var absoluteSeconds = Math.floor(seconds);
    var s = absoluteSeconds > 9 ? absoluteSeconds : '0' + absoluteSeconds;

    return 'Minutes: ' + h + ' Seconds:  ' + m + '  Timer: ' + s;
  }
  function setTimer( remain, actions ) {
    var remain = remain*60000;
    var action;
    (function countdown() {
       display("timer", toMinuteAndSecond(remain));
       if (action = actions[remain]) {
         action();
       }
       if (remain > 0) {
         if( remain == 0){
           act();
         }
         remain -= 200;
         setTimeout(arguments.callee, 1);
       }
    })(); 
  }

  setTimer(<?php echo 0.2*60;  ?>, {
    2400: function () { display("notifier", "40 minutes left"); },
     900: function () { display("notifier", "15 to go");        },
     0: function () { display("notifier", "Time is up. You cannot continue, please click on SEND FOR MARKING to finish"); act();}
  });
})()
</script>
<input type="text" name="totalsquestions" value="<?php echo $loop_questions; ?>" hidden ="hidden"/>
<input type="submit" name="mark" class="btn btn-primary" value="Send for Marking" />
</form>
</div>
<?php
}else{
    echo "This paper has no questions set yet";
} 
}
?>