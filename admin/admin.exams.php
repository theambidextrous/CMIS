<?php
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once("$class_dir/EvarsitySMS.php");
?>
<script language="javascript" type="text/javascript">
<!--
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> | Exams";
//-->
$(document).ready(function() {
	//Load TinyMCE	
	tinymce.init({		
		selector: 'textarea.tinymce',
		height: 150,
		menubar: false,
		plugins: [
			'advlist autolink lists link image charmap print preview anchor textcolor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table contextmenu paste code help wordcount'
		],
		toolbar: 'insert | undo redo | styleselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
		content_css: "<?=SYSTEM_URL;?>/styles/tinymce.editor.css"
	});
	
	$('#collapse1').addClass('in');
	
});
</script>
<div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-labelledby="notify" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Notifications</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="alert-success">Update was successful</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>
<div class="row">
<div class="col-lg-12">
<h1 class="page-header">Exams</h1>
</div>
<!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
<div class="col-lg-12">
<div class="panel panel-default">
    <div class="panel-heading"> <i class="fa fa-users book-fw"></i> Manage Exams </div>
    <!-- /.panel-heading -->
    <div class="panel-body">

    <ul class="nav nav-tabs cookie">
        <li class="active"><a data-toggle="tab" href="#tabs-1" title="Students"><span>Exams</span></a></li>
        <li><a data-toggle="tab" href="#tabs-2" title="Announcements"><span>Exam Questions</span></a></li>
        <li><a data-toggle="tab" href="#tabs-3" title="Login History"><span>Exams Preview</span></a></li>
    </ul>
    <div class="tab-content">
        <div id="tabs-1" class="tab-pane active">
        <!--Begin Forms-->
        <?php
        $a = isset($_GET["task"])?$_GET["task"]:"";
        $recid = intval(! empty($_GET['recid']))?$_GET['recid']:0;            
        $LoginID = !empty($_GET['examID'])?$_GET['examID']:"";
        
        switch ($a) {
        case 'do':
            $do = !empty($_GET['do'])?$_GET['do']:"";
            switch($do){
                case 'activate':
                $student = $_GET['recid'];
                $exam = $_GET['examID'];
                activateExam($student, $exam, "S", "1");
                redirect("admin.php?dispatcher=exams&task=view&recid=0&examID=1#sub-tabs-3");
                break;
                case 'diactivate':
                break;
                case 'activate_l':
                $Faculty = $_GET['recid'];
                $exam = $_GET['examID'];
                activateExam($Faculty, $exam, "F", "1");
                redirect("admin.php?dispatcher=exams&task=view&recid=0&examID=1#sub-tabs-3");
                $_SESSION['MSG'] = ConfirmMessage("Faculty has been notified");
                break;
                case 'diactivate_l':
                break;
                case 'activateAll':
                $student = "default";
                $exam = $_GET['examID'];
                activateExam($student, $exam, "S", "0");
                redirect("admin.php?dispatcher=exams");
                echo ConfirmMessage("Exam has been updated and students notified");
                break;
                case 'diactivateAll':
                break;
            }
            break;
        case "add":
            addrec();
            break;
        case "view":
            viewrec($recid);
            break;
        case "edit":
            editrec($recid);
            break;
        case "del":
            deleterec($recid);
            break;
        default:
            // print_r(getUnitEnrolledStudents("COT003"));
            select();
            break;
        }		
        ?>
        <!--End Forms-->
        </div>
        <div id="tabs-2" class="tab-pane">
        <!--Begin Forms-->        
        <?php manage_exam_questions(); ?>
        <!--End Forms-->
        </div>
        <div id="tabs-3" class="tab-pane">
        <!--Begin Forms-->
        <?php preview(); ?>
        <!--End Forms-->
        </div>
    </div>
    <!-- /.tab-content -->
    </div>
    <!-- /.panel-body --> 
</div>
<!-- /.panel-default --> 
</div>
<!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<?php 
function select(){
global $a;
global $filter;
global $filterfield;

if ($a == "reset") {
    $filter = "";
    $filterfield = "";
}

$res = sql_select();
$count = sql_getrecordcount();	

if(isset($_GET['enable']) && isset($_GET['examID'])){
    $disabledFlag = intval(! empty($_GET['enable']))?$_GET['enable']:0;
    $editID = intval(! empty($_GET['examID']))?$_GET['examID']:0;
    sql_update_status($disabledFlag, $editID);
}
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li class="active">Available Exams</li></ol>

<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>

<?php showpagenav($pagecount); ?>
<table width="100%" class="display table table-striped table-bordered table-hover">
<thead>
<tr>
<th>Exam ID</th>
<th>Exam Name</th>
<th>Exam Course</th>
<th>Exam Unit</th>
<th>Exam Date</th>
<th>Exam Deadline</th>
<th>Duration(Min)</th>
<th class="no-sort">Status</th>
<th class="no-sort">Actions</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $count; $i++){
$row = db_fetch_array($res);
?>
<tr>
<td><?=$row["ExamID"]?></td>
<td><?=$row["ExamName"]?></td>
<td><?=$row["ExamCourse"]?></td>
<td><?=$row["ExamUnit"]?></td>
<td><?=$row["ExamDate"]?></td>
<td><?=$row["ExamDeadline"]?></td>
<td><?=$row["ExamDuration"]?></td>
<?php
if($row['disabledFlag'] == 0){
echo "<td align=\"center\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/yes.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['ExamName']."\">Open to Students</td>";
}else{
echo "<td align=\"center\"><a href=\"admin.php?dispatcher=exams&task=do&do=activateAll&enable=0&examID=".$row['ExamID']."\" title=\"Click to open ".$row['ExamName']."\"><img border=\"0\" src=\"".IMAGE_FOLDER."/icons/no.png\" height=\"12\" width=\"12\" alt=\"Disable ".$row['ExamName']."\"></a> Not Open to Students</td>";
}
?>
<td><a href="admin.php?dispatcher=exams&task=view&recid=<?=$i ?>&examID=<?=$row['ExamID'] ?>">Manage</a> | <a href="admin.php?dispatcher=exams&task=edit&recid=<?=$i ?>&examID=<?=$row['ExamID'] ?>">Edit</a> | <a href="admin.php?dispatcher=exams&task=del&recid=<?=$i ?>&examID=<?=$row['ExamID'] ?>">Delete</a></td>
</tr>        
<?php
}
db_free_result($res);
?></tbody>
</table>
<?php 
showpagenav($pagecount);
unset($_SESSION['MSG']);
} 
?>
<?php function showrow($row, $recid){?>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<tr>
<td width="30%">Exam ID</td>
<td><?=$row["ExamID"]; ?></td>
</tr>
<tr>
<td>Exam Name</td>
<td><?=$row["ExamName"]; ?></td>
</tr>
</table>
</div>
<?php } ?>

<?php 
function showrowdetailed($row, $recid){
global $conn,$class_dir;
?>
<div id="hideMsg"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></div>

<div class="head-details">
<h2 class="text-uppercase text-primary"><?=$row["ExamName"]; ?> <span class="small text-muted"><?=$row["ExamID"]; ?></span></h2>
</div>

<div id="adv-tab-container">
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#sub-tabs-1" title="<?=SYSTEM_SHORT_NAME?> | Exam Details">Exam Details</a></li>
    <li><a data-toggle="tab" href="#sub-tabs-2" title="<?=SYSTEM_SHORT_NAME?> | Questions">Add Exam Questions</a></li>
        <li><a data-toggle="tab" href="#sub-tabs-3" title="<?=SYSTEM_SHORT_NAME?> | Upoads">Associated Persons</a></li>
</ul>
<div class="tab-content">
<!--sub-tabs-1-->
<div id="sub-tabs-1" class="tab-pane active">
    <h3>Exam Details</h3>
    <div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
        <tr><td><strong>Exam ID:</strong> </td><td><?=$row["ExamID"]; ?></td></tr>
        <tr><td><strong>Exam Course:</strong> </td><td><?=$row["ExamCourse"]; ?></td></tr>
        <tr><td><strong>Exam Unit:</strong> </td><td><?=$row["ExamUnit"]; ?></td></tr>        
        <tr><td><strong>Exam Instructons:</strong></td><td><?=$row["ExamInstructions"]; ?></td></tr>
        <tr><td><strong>Exam Duration:</strong> </td><td><?=$row["ExamDuration"]; ?></td></tr>
        <tr><td><strong>Exam Date:</strong> </td><td><?=$row["ExamDate"]; ?></td></tr>
        <tr><td><strong>Exam Deadline:</strong> </td><td><?=$row['ExamDeadline']?></td></tr>
        </table>
    </div>
    </div>
</div>
<!--sub-tabs-2-->
<div id="sub-tabs-2" class="tab-pane">
    <h3>Exam Questions</h3>
    <?php			
        $ExamID = $row["ExamID"];
    $ExamQuestions = getExamQuestions($ExamID);
    //create cases for exam questions options : drop, disable		
    if(!empty($ExamQuestions)){
        //Actions
      $Action = !empty($_POST['action'])?$_POST['action']:"";
      $Action1 = !empty($_GET['action'])?$_GET['action']:"";
      //add question to this paper
      if(!empty($Action1) && !empty($ExamID)){
        if(isset($_POST['Add'])){
            //make question ID
            $maxidsql = "SELECT MAX(`QuestionID`) AS 'qid' FROM `".DB_PREFIX."exam_questions`";
            $maxidres = db_query($maxidsql,DB_NAME,$conn);	
            $maxidrow = db_fetch_array($maxidres);
            $questionID = $maxidrow['qid'] + 1;
            //create posts
            $FIELDS['exam'] = secure_string($_POST['exam']);
            $FIELDS['question'] = encode(secure_string($_POST['question']));
            $FIELDS['type'] = secure_string($_POST['type']);
            $FIELDS['answer'] = $_POST['answer'];
            $FIELDS['answeropen'] = encode(secure_string($_POST['answeropen']));
            $FIELDS['marking'] = $_POST['marking'];
            $FIELDS['credits'] = secure_string($_POST['credits']);
            $FIELDS['OptionID'] = $_POST['OptionID'];
            $FIELDS['OptionValue'] = $_POST['OptionValue'];
            //update
            $sql = sprintf("INSERT INTO `".DB_PREFIX."exam_questions` (`QuestionID`, `ExamID`, `Question`, `QuestionType`, `AnswerOption`, `AnswerOptionOpen`, `SystemMarked`, `Credits`) VALUES (%d, %d, '%s', '%s', '%s', '%s', %d, '%s')", $questionID, $FIELDS['exam'], $FIELDS['question'], $FIELDS['type'], $FIELDS['answer'], $FIELDS['answeropen'], $FIELDS['marking'], $FIELDS['credits']);	
            db_query($sql,DB_NAME,$conn);
            //Check if added
            if(db_affected_rows($conn)){
                //add options
               // print_r($FIELDS['OptionID']);
                $count = 0;
                foreach($FIELDS['OptionID'] as $Op):
                $is_answer = 0;
                if($FIELDS['answer'] == $Op){
                $is_answer = 1;
                }
                $sql = sprintf("INSERT INTO `".DB_PREFIX."exam_question_options` (`QuestionID`, `OptionName`, `OptionIdentity`, `IsAnswer`) VALUES (%d, '%s', '%s', %d)", $questionID, $FIELDS['OptionValue'][$count], $Op, $is_answer);
                db_query($sql,DB_NAME,$conn);
                    $count++;
                endforeach;
            $_SESSION['MSG'] = ConfirmMessage("Question Added successfully.");
            }else{
            $_SESSION['MSG'] = WarnMessage("No changes made!");
            }
            //redirect("admin.php?dispatcher=exams");
        }

      }
        if(!empty($Action) && !empty($ExamID)){
            $selectedExam = !empty($_GET['ExamID'])?$_GET['ExamID']:0;
                        //Action
            switch($Action){
                case "Disable":
                if(!empty($_POST['QuestionIDs'])){
                    foreach($_POST['QuestionIDs'] as $QID){
                        $sqlUdateExamQuestions = sprintf("UPDATE `".DB_PREFIX."exam_questions` SET `disabledFlag` = %d WHERE `ExamID` = %d AND `QuestionID` = %d", 1, $ExamID,$QID);
                        db_query($sqlUdateExamQuestions,DB_NAME,$conn);							
                    }
                    // Confirm
                    $_SESSION['MSG'] = ConfirmMessage("Selected questions have been disabled!");
                }
                break;
                case "drop":
                if(!empty($_POST['QuestionIDs'])){
                    foreach($_POST['QuestionIDs'] as $QID){
                        $sqlUdateExamQuestions = sprintf("UPDATE `".DB_PREFIX."exam_questions` SET `deletedFlag` = %d WHERE `ExamID` = %d AND `QuestionID` = %d", 1, $ExamID,$QID);
                        db_query($sqlUdateExamQuestions,DB_NAME,$conn);	
                    }
                    // Confirm
                    $_SESSION['MSG'] = ConfirmMessage("Selected questions have been dropped!");
                }
                break;
                case "Enable":
                if(!empty($_POST['QuestionIDs'])){
                    foreach($_POST['QuestionIDs'] as $QID){
                        $sqlUdateExamQuestions = sprintf("UPDATE `".DB_PREFIX."exam_questions` SET `disabledFlag` = %d WHERE `ExamID` = %d AND `QuestionID` = %d", 0, $ExamID,$QID);
                        db_query($sqlUdateExamQuestions,DB_NAME,$conn);	
                    }
                    // Confirm
                    $_SESSION['MSG'] = ConfirmMessage("Selected questions have been enabled!");
                }
                break;
                case "add":									
                
                break;
                case "reject":
                    
                break;
            }
                            
        }
        ?>
        <script>
        //<!--
        function checkQuestions(field){
            if(document.units.sel.checked == true){
                for(var i=0; i < field.length; i++){
                    field[i].checked=true;
                }
            }
            else{
                for(var i=0; i < field.length; i++){
                    field[i].checked=false;
                }
            }
        }
        //-->
        </script>
        <div class="modal fade" id="addUnits" tabindex="-1" role="dialog" aria-labelledby="addUnitsLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form class="form" name="assign-lectures" method="post" action="admin.php?dispatcher=exams&task=view&recid=<?=$recid?>&examID=<?=$row["ExamID"]?>&action=add">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addUnitsLabel">Add questions</h4>
            </div>
            <div class="modal-body">
                <h2>Question</h2>
            
                <div class="form-group">
        <div class="row">
            <div class="form-group col-sm-6">
                <label for="course" class="">Select Exam Paper: <abbr class="text-danger" title="required">*</abbr></label> <?php echo sqlOption("SELECT `ExamID`,`ExamName` FROM `".DB_PREFIX."exams` WHERE `deletedFlag` = 0","exam",$FIELDS['exam'],"--Select exam paper--");?>
                <span class="text-danger"><?=$ERRORS['exam'];?></span>
            </div>
            <div class="form-group col-sm-12">
        <label for="question" class="">Question(include illustrations where applicable) <abbr class="text-danger" title="required">*</abbr></label>
        <textarea class="form-control tinymce" name="question" rows="2"><?=$FIELDS['question'];?></textarea>
        <span class="text-danger"><?=$ERRORS['question'];?></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-6">
                <label for="type" class="">Question Type: <abbr class="text-danger" title="required">*</abbr></label>
                <select name="type" class="form-control required">
                <option value="Closed">Closed Ended</option>
                <option value="Open">Open Ended</option>
                </select>
                <span class="text-danger"><?=$ERRORS['type'];?></span>
            </div>
            <div class="form-group col-sm-6">
                <label for="answer" class="">Question Answer(if closed)<abbr class="text-danger" title="required">*</abbr></label>
                <select name="answer" class="form-control required">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="F">F</option>
                <option value="G">G</option>
                <option value="H">H</option>
                <option value="I">I</option>
                <option value="J">J</option>
                <option value="Z">No answer option/Open Question </option>
                </select>
                <span class="text-danger"><?=$ERRORS['answer'];?></span>
            </div>
            <div class="form-group col-sm-12">
                <label for="question" class="">Answer(For open Question) <abbr class="text-danger" title="required">*</abbr></label>
                <textarea class="form-control tinymce" name="answeropen" rows="2"><?=$FIELDS['answeropen'];?></textarea>
                <span class="text-danger"><?=$ERRORS['answeropen'];?></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-6">
                <label for="marking" class="">Marking Type: <abbr class="text-danger" title="required">*</abbr></label>
                <select name="marking" class="form-control required">
                <option value="1">System Marked</option>
                <option value="2">Lecturer marked(open questions)</option>
                </select>
                <span class="text-danger"><?=$ERRORS['marking'];?></span>
            </div>
            <div class="form-group col-sm-6">
                <label for="credits" class="">Question marks(credits)<abbr class="text-danger" title="required">*</abbr></label>
                <input type="text" class="form-control required" name="credits" id="credits" value="<?=$FIELDS['credits'];?>">
                <span class="text-danger"><?=$ERRORS['credits'];?></span>
            </div>
        </div>
        <div class="row" id="Qoptions">
            <div class="form-group col-sm-6">
            <h2>Question Options</h2>
            <script>
      jQuery(function($) {
          var max_fields      = 10; //maximum input boxes allowed
          var wrapper         = $(".input-fields-wrapper"); //Fields wrapper
          var add_button      = $(".add_field_button"); //Add button ID
          
          var x = 1; //initlal text box count
          $(add_button).click(function(e){ //on add input button click
              e.preventDefault();
              if(x < max_fields){ //max input box allowed
                  x++; //text box increment
                  $(wrapper).append('<div class="row multi-fields"><span class="col-xs-5"><input class="form-control" type="text" name="OptionID[]" placeholder="e.g. C"></span><span class="col-xs-5"><input class="form-control" type="text" name="OptionValue[]" placeholder="option value"></span><a href="#" class="remove_field col-xs-2 btn btn-sm btn-danger">X</a></div>'); //add input box
              }
          });
          
          $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
              e.preventDefault(); $(this).parent('div').remove(); x--;
          })
      });
      </script>
      <div class="input-fields-wrapper">        
        <div class="row multi-fields"><span class="col-xs-5"><input class="form-control" type="text" name="OptionID[]" placeholder="e.g. C"></span><span class="col-xs-5"><input class="form-control" type="text" name="OptionValue[]" placeholder="option value"></span><span class="col-xs-2"></span></div>
      </div>
      <a href="#" class="add_field_button">+ Option</a>
            </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" name="Add" value="Add" />
        </div>
        </div>
        </div><!-- /.modal-content -->
        </form><!-- /END ADD QUESTION NAME-->
        </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <form name="units" method="post" action="admin.php?dispatcher=exams&task=view&recid=<?=$recid?>">
        <div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
        <p class="text-right"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUnits">Add a Question</button></p>
        <table width="100%" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
        <th width="20">Question ID</th>
        <th>Question</th>
        <th>Type</th>
        <th>Ans Option</th>
        <th>Marking</th>
        <th>Credits</th>
        <th>Status</th>
        <th style="text-align:center"><input type="checkbox" name="sel" title="Check All" onclick="checkQuestions(document.getElementsByName('QuestionIDs[]'));" value="" /></th>
        </tr>
        </thead>
        <tbody>
        <?php			
            $resQuestions = getExamQuestions($ExamID);			
            if(db_num_rows($resQuestions)>0){				
                $count = 1;
        while($questions = db_fetch_array($resQuestions)){
        if($questions['SystemMarked']==1){$markType = "System Marked";}else{$markType = "Manual";}
        if($questions['disabledFlag']==1){$status = "Disabled";}else{$status = "Enabled";}
            echo "<tr>
            <td>".$questions['QuestionID']."</td>
            <td>".decode($questions['Question'])."</td>
            <td>".$questions['QuestionType']."</td>
            <td>".$questions['AnswerOption']."</td>
            <td>".$markType."</td>
            <td>".$questions['Credits']."</td>
            <td>".$status."</td>
            <td align=\"center\"><input type=\"checkbox\" id=\"selectedIDs\" name=\"QuestionIDs[]\" value=\"".$questions['QuestionID']."\"></td>
            </tr>";
            $count++;
        }
            }else{
                echo "<tr><td colspan=\"6\">This paper has no questions set yet</td></tr>";
            }
        ?>
        </tbody>
        <tfoot>
        <tr><td align="right" colspan="6">
        <div class="form-inline">
        <div class="form-group">
            <label>With selected:</label>&nbsp;<select name="action" class="form-control">
            <option value="Disable">Disable</option>
            <option value="Enable">Enable</option>
            <option value="Drop">Drop</option>
            </select>&nbsp;<input class="btn btn-default" type="submit" name="Update" value="Go" />
        </div>
        </div>
        </td></tr>
        </tfoot>
        </table>
        </form>
        <?php
        unset($_SESSION['MSG']);			
    }else{
        echo "<p>This paper has no questions.</p>";
    }
    ?>
</div>
    
    <!--sub-tabs-3-->
<div id="sub-tabs-3" class="tab-pane">
    <?php 
        //$facultyIDs = getExamFacultyID($row['ExamUnit']);
        $faculties = array();
        foreach(getExamFacultyID($row['ExamUnit']) as $f):
        $getExamLecturers = "SELECT *,CONCAT(`FName`,' ',`LName`) AS `FacultyName` FROM `".DB_PREFIX."faculties` WHERE disabledFlag = 0 AND deletedFlag = 0 AND `FacultyID` = '".$f."'";
        $result = db_query($getExamLecturers,DB_NAME,$conn);
        $r = db_fetch_array($result);
        array_push($faculties, $r);
        endforeach;
    ?>
        <h3>Lecturers</h3>
        <table width="100%" class="display table table-striped table-bordered table-hover">
        <thead>
        <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th class="no-sort">Actions</th>
        </tr>
        </thead>
        <?php
        echo "<tbody>";
        foreach($faculties as $fa):
            echo '<tr>
            <td>'.$fa['FacultyID'].'</td>
            <td>'.$fa['FacultyName'].'</td>
            <td>'.$fa['Email'].'</td>
            <td>'.$fa['MPhone'].'</td>
            <td><a href="admin.php?dispatcher=exams&task=do&do=activate_l&recid='.$fa['FacultyID'].'&examID='.$row['ExamID'].'" onclick="alert("Notified!");">'.isnotified($fa['FacultyID'], $row['ExamID']).'</a>
            </tr>';	
        endforeach;
        echo "</tbody>";
        ?>
        </table>
        <?php
        $getExamStudents = "SELECT *,CONCAT(`FName`,' ',`LName`) AS `StudentName` FROM `".DB_PREFIX."students` WHERE disabledFlag = 0 AND deletedFlag = 0 AND `Courses` LIKE '%".$row['ExamCourse']."%'";
        //run the query
        $result = db_query($getExamStudents,DB_NAME,$conn);
        ?>
        <h3>Students</h3>
        <table width="100%" class="display table table-striped table-bordered table-hover">
        <thead>
        <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Courses</th>
        <th class="no-sort">Activate Exam</th>
        </tr>
        </thead>
        <?php
        //check if any rows returned
        if(db_num_rows($result)>0){
            echo "<tbody>";
            while($r = db_fetch_array($result)){
                echo '<tr>
                <td>'.$r['StudentID'].'</td>
                <td>'.$r['StudentName'].'</td>
                <td>'.$r['Email'].'</td>
                <td>'.$r['Phone'].'</td>
                <td>'.$r['Courses'].'</td>
                <td><a href="admin.php?dispatcher=exams&task=do&do=activate&recid='.$r['StudentID'].'&examID='.$row['ExamID'].'">'.isnotifiedS($r['StudentID'], $row['ExamID']).'</a>
                </tr>';		
            }
            echo "</tbody>";
        }else{
            
        }
        ?>
        </table>
    </div>

<div class="quick-nav btn-group">
    <a class="btn btn-default" href="admin.php?dispatcher=exams&task=add"><i class="fa fa-file-o fa-fw"></i>Add Exam</a>
    <a class="btn btn-default" href="admin.php?dispatcher=exams&task=edit&recid=<?=$recid ?>"><i class="fa fa-pencil-square-o fa-fw"></i>Edit Exam</a>
    <a class="btn btn-default" href="admin.php?dispatcher=exams&task=del&recid=<?=$recid ?>"><i class="fa fa-trash-o fa-fw"></i>Delete Exam</a>
</div>

</div>
</div>

<?php } ?>
<?php 
function showroweditor($row, $iseditmode, $ERRORS){
global $a;
?>
<p class="text-center lead"><strong><?=strtoupper($a)?> EXAM DETAILS</strong></p>
<p class="text-center small"><span class="text-danger">FIELDS MARKED WITH ASTERISKS (*) ARE REQUIRED</span></p>

<h2>Exam Information</h2>

<div class="row">
<div class="col-md-4">
<div class="form-group">
  <label for="">Examination ID: <span class="text-danger">*</span></label>
  <i><?=!empty($row['ExamID'])?$row['ExamID']:" a new Exam ID will be generated automatically"; ?></i>
  </div>
</div>
<div class="col-md-4">
<div class="form-group">
<label for="">Examination Name: <span class="text-danger">*</span></label>
<input <?=$ERRORS['ExamName']?> type="text" value="<?=$row['ExamName']; ?>" name="ExamName" class="form-control required" />
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label for="">Examination Course:</label>
<?php echo sqlOption("SELECT `CourseID`,`CName` FROM `".DB_PREFIX."courses` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","ExamCourse",$row['ExamCourse'],"--Select course--");?>
<span class="text-danger"><?=$ERRORS['ExamCourse'];?></span>
</div>
</div>
</div>

<div class="row">
<div class="col-md-4">
<div class="form-group">
<label for="">Examination Course Unit: <span class="text-danger">*</span></label>
<?php echo sqlOption("SELECT `UnitID`,`UName` FROM `".DB_PREFIX."units` WHERE `disabledFlag` = 0 AND `deletedFlag` = 0","ExamUnit",$row['ExamUnit'],"--Select unit--");?>
<span class="text-danger"><?=$ERRORS['ExamUnit'];?></span>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label for="">Exam Instructions: <span class="text-danger">*</span></label>
<input <?=$ERRORS['ExamInstructions']?> type="text" value="<?=$row['ExamInstructions']; ?>" name="ExamInstructions" class="form-control required" />
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label for="">Examination Date: <span class="text-danger">*</span></label>
<input <?=$ERRORS['ExamDate']?> type="text" value="<?=$row['ExamDate']; ?>" name="ExamDate" class="form-control datepicker required" />
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label for="">Examination Deadline: <span class="text-danger">*</span></label>
<input <?=$ERRORS['ExamDeadline']?> class="form-control datepickerfrom required" type="text" value="<?=$row['ExamDeadline']; ?>" name="ExamDeadline" />
</div>
</div>
</div>
<div class="row">
    <div class="col-md-4">
    <div class="form-group">
    <label for="">Examination Duration:(in minutes)</label>
    <input <?=$ERRORS['ExamDuration']?> type="text" value="<?=$row['ExamDuration']; ?>" name="ExamDuration" class="form-control required" />
    </div>
    </div>
</div>

</div>
</div>
<?php } ?>

<?php
function showpagenav() {
?>
<div class="quick-nav btn-group">
<a class="btn btn-primary" href="admin.php?dispatcher=exams&task=add">Create Exam</a>
<a class="btn btn-default" href="admin.php?dispatcher=exams&task=reset">Reset Filters</a>
</div>
<?php } ?>

<?php function showrecnav($a, $recid, $count) { ?>
<div class="quick-nav btn-group">
<a class="btn btn-default" href="admin.php?dispatcher=exams"><i class="fa fa-undo fa-fw"></i> Back to Exams</a>
<?php if ($recid > 0) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=exams&task=<?=$a ?>&recid=<?=$recid - 1 ?>"><i class="fa fa-arrow-left fa-fw"></i> Prior Record</a>
<?php } if ($recid < $count - 1) { ?>
<a class="btn btn-default" href="admin.php?dispatcher=exams&task=<?=$a ?>&recid=<?=$recid + 1 ?>"><i class="fa fa-arrow-right fa-fw"></i> Next Record</a>
<?php } ?>
</div>
<?php } ?>

<?php 
function viewrec($recid){

$res = sql_select();
$count = sql_getrecordcount();
db_data_seek($res, $recid);
$row = db_fetch_array($res); 

if($row['disabledFlag'] == 0){
    $row["Status"] = "Enabled";
}else{
    $row["Status"] = "Disabled";
}	 
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=exams">Exams</a></li><li class="active">View Exams</li></ol>
<?php 
showrecnav("view", $recid, $count);
showrowdetailed($row, $recid);
db_free_result($res);
} 
?>

<?php 
function addrec() {
global $class_dir,$conn;
require_once("$class_dir/class.validator.php3");

// Variables
$ERRORS = array();
$FIELDS = array();
$ERR = 'id="highlight"';//Error highlighter

// Commands
if(isset($_POST["Add"])){
    // exam info		
    $FIELDS['ExamName'] = secure_string($_POST['ExamName']);
    $FIELDS['ExamCourse'] = secure_string($_POST['ExamCourse']);
    $FIELDS['ExamUnit'] = secure_string($_POST['ExamUnit']);
    $FIELDS['ExamInstructions'] = secure_string($_POST['ExamInstructions']);
    $FIELDS['ExamDate'] = secure_string($_POST['ExamDate']);
    $FIELDS['ExamDeadline'] = secure_string($_POST['ExamDeadline']);
    $FIELDS['ExamDuration'] = secure_string($_POST['ExamDuration']);
    // Validator data
    $check = new validator();
    // validate entry		
    // validate "FName" field
    // if(!$check->is_String($FIELDS['ExamName']))
    // $ERRORS['ExamName'] = $ERR;
    // // validate "LName" field
    // if(!$check->is_String($FIELDS['ExamCourse']))
    // $ERRORS['ExamCourse'] = $ERR;
    if(sizeof($ERRORS) > 0){
        $ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");			
    }
    else{
        $maxidsql = "SELECT MAX(`ExamID`) AS 'eid' FROM `".DB_PREFIX."exams`";
            $maxidres = db_query($maxidsql,DB_NAME,$conn);	
            $maxidrow = db_fetch_array($maxidres);
            $FIELDS['ExamID'] = $maxidrow['eid'] + 1;
        if(sql_insert($FIELDS)){
            //Display Confirmation Message
            $_SESSION['MSG'] = ConfirmMessage("New Exam has been added successfully.");
            redirect("admin.php?dispatcher=exams");
        }else{
            //Display Error Message
            $ERRORS['MSG'] = ErrorMessage("Failed to create new exam. Check to confirm if all fields are well populated and try again.");
        }
        
    }
}
    
$row["ExamName"] = !empty($FIELDS['ExamName'])?$FIELDS['ExamName']:"";
$row["ExamCourse"] = !empty($FIELDS['ExamCourse'])?$FIELDS['ExamCourse']:"";
$row["ExamUnit"] = !empty($FIELDS['ExamUnit'])?$FIELDS['ExamUnit']:"";
$row["ExamInstructions"] = !empty($FIELDS['ExamInstructions'])?$FIELDS['ExamInstructions']:"";
$row["ExamDate"] = !empty($FIELDS['ExamDate'])?$FIELDS['ExamDate']:"";
$row["ExamDeadline"] = !empty($FIELDS['ExamDeadline'])?$FIELDS['ExamDeadline']:"";
$row["ExamDuration"] = !empty($FIELDS['ExamDuration'])?$FIELDS['ExamDuration']:"";
$row["ExamID"] = !empty($FIELDS['ExamID'])?$FIELDS['ExamID']:"";
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=exams">Exams</a></li><li class="active">Create Exam</li></ol>

<a class="btn btn-default" href="admin.php?dispatcher=exams"><i class="fa fa-undo fa-fw"></i> Back to Exams</a>

<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=exams&task=add" method="post">
<input type="hidden" name="sql" value="insert" />
<?php
showroweditor($row, false, $ERRORS);
?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Add" value="Save" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=exams'" />
</p>
</form>
<?php } ?>

<?php 
function editrec($recid){
global $class_dir;
require_once("$class_dir/class.validator.php3");

// Variables
$ERRORS = array();
$FIELDS = array();
$ERR = 'id="highlight"';//Error highlighter

// Commands
if(isset($_POST["Edit"])){
    // exam info		
    $FIELDS['ExamName'] = secure_string($_POST['ExamName']);
    $FIELDS['ExamCourse'] = secure_string($_POST['ExamCourse']);
    $FIELDS['ExamUnit'] = secure_string($_POST['ExamUnit']);
    $FIELDS['ExamInstructions'] = secure_string($_POST['ExamInstructions']);
    $FIELDS['ExamDate'] = secure_string($_POST['ExamDate']);
    $FIELDS['ExamDeadline'] = secure_string($_POST['ExamDeadline']);
    $FIELDS['ExamDuration'] = secure_string($_POST['ExamDuration']);
    // Validator data
    $check = new validator();
    // validate entry		
    // validate "LName" field
    if(!$check->is_String($FIELDS['ExamCourse'])){
    $ERRORS['ExamCourse'] = $ERR;}
    if(empty($FIELDS['ExamName'])){
        $ERRORS['ExamName'] = $ERR;}
    if(empty($FIELDS['ExamInstructions'])){
        $ERRORS['ExamInstructions'] = $ERR;}

    if(sizeof($ERRORS) > 0){
        $ERRORS['MSG'] = ErrorMessage("PLEASE CORRECT HIGHLIGHTED FIELDS!");			
    }
    else{			
        if(sql_update($FIELDS)) {
            $mail = new PHPMailer;
            //Send a message to user
            $bodyemail='<div style="background-color:#E1CDB7; color:#000; width:600px;">
            <div style="background:#C60; color:#FFF; min-width:584px; padding:8px;">
            <h1 style="font-size:15; font-weight:700;line-height:25px;"><em>'.$FIELDS['ExamName'].' was updated</em></h1>
            </div>
            <div style="padding:15px;">
            <h3 style="color:#333;">Dear Admin,</h3>
            <p style="text-align:justify;">The above exam has been updated by Admin.</p>
            <p>ICT Department,<br />
            '.SYSTEM_NAME.',<br />
            '.COMPANY_ADDRESS.'<br />
            TEL: '.COMPANY_PHONE.'<br />
            EMAIL: '.INFO_EMAIL.'<br />
            WEBSITE: '.PARENT_HOME_URL.'</p>
            </div>
            </div>';
            
            $body = preg_replace('/\\\\/','', $bodyemail); //Strip backslashes
                                    
            switch(MAILER){
            case 'smtp':
            $mail->isSMTP(); // telling the class to use SMTP
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = SMTP_AUTH; // enable SMTP authentication
            $mail->SMTPSecure = SMTP_SECU; // sets the prefix to the servier
            $mail->Host = SMTP_HOST; // SMTP server
            $mail->Port = SMTP_PORT; // set the SMTP port for the HOST server
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            break;
            case 'sendmail':
            $mail->isSendmail(); // telling the class to use SendMail transport
            break;
            case 'mail':
            $mail->isMail(); // telling the class to use mail function
            break;
            }
            
            $mail->setFrom(SUPPORT_EMAIL, SUPPORT_NAME);
            $mail->Subject = $FIELDS['ExamName']." - Updated";
            $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
            $mail->msgHTML($body);
            $mail->addAddress(MAILER_FROM_EMAIL,  MAILER_FROM_NAME);
            //$mail->addBCC(INFO_EMAIL, INFO_NAME);
            
            if(!$mail->Send()) {
                //Display Confirmation Message
                $_SESSION['MSG'] = ConfirmMessage("Exam details have been updated successfully");
                redirect("admin.php?dispatcher=exams");
            }else{
                //Display Confirmation Message
                $_SESSION['MSG'] = ConfirmMessage("Exam details have been updated and emailed successfully.");
                redirect("admin.php?dispatcher=exams");
            }
        }
        else{
            //Display Error Message
            $ERRORS['MSG'] = ErrorMessage("No changes made. Check to confirm if all fields are well populated and try again.");
        }
    }
}

$res = sql_select();
$count = sql_getrecordcount();
db_data_seek($res, $recid);
$row = db_fetch_array($res);
    
$row["ExamName"] = !empty($FIELDS['ExamName'])?$FIELDS['ExamName']:$row['ExamName'];
$row["ExamCourse"] = !empty($FIELDS['ExamCourse'])?$FIELDS['ExamCourse']:$row['ExamCourse'];
$row["ExamUnit"] = !empty($FIELDS['ExamUnit'])?$FIELDS['ExamUnit']:$row['ExamUnit'];
$row["ExamInstructions"] = !empty($FIELDS['ExamInstructions'])?$FIELDS['ExamInstructions']:$row['ExamInstructions'];
$row["ExamDate"] = !empty($FIELDS['ExamDate'])?$FIELDS['ExamDate']:$row['ExamDate'];
$row["ExamDeadline"] = !empty($FIELDS['ExamDeadline'])?$FIELDS['ExamDeadline']:$row['ExamDeadline'];
$row["ExamDuration"] = !empty($FIELDS['ExamDuration'])?$FIELDS['ExamDuration']:$row['ExamDuration'];
$row["ExamID"] = !empty($FIELDS['ExamID'])?$FIELDS['ExamID']:$row['ExamID'];
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=exams">Exams</a></li><li class="active">Edit Exams</li></ol>
<?php showrecnav("edit", $recid, $count); ?>
<form id="validateform" enctype="multipart/form-data" action="admin.php?dispatcher=exams&task=edit&recid=<?=$recid?>" method="post">
<p class="text-center"><?php if(sizeof($ERRORS['MSG'])>0) echo $ERRORS['MSG'];?></p>
<input type="hidden" name="sql" value="update" />
<input type="hidden" name="eid" value="<?=$row["ExamID"] ?>" />
<input type="hidden" name="StudentID" value="<?=$row["ExamID"] ?>" />
<?php showroweditor($row, true, $ERRORS); ?>
<p class="text-center">
<input class="btn btn-primary" type="submit" name="Edit" value="Save" />
<input class="btn btn-default" type="button" name="cancel" value="Cancel" onclick="javascript:location.href='admin.php?dispatcher=exams'" />
</p>
</form>
<?php
db_free_result($res);
} 
?>

<?php 
function deleterec($recid){

// Commands
if(isset($_POST["Delete"])){
    sql_delete();
}

$res = sql_select();
$count = sql_getrecordcount();
db_data_seek($res, $recid);
$row = db_fetch_array($res);  
?>
<ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=exams">Exam</a></li><li class="active">Delete Exam</li></ol>
<?php showrecnav("del", $recid, $count); ?>
<form action="admin.php?dispatcher=exams&task=del&recid=<?=$recid?>" method="post">
<input type="hidden" name="sql" value="delete" />
<input type="hidden" name="eid" value="<?=$row["ExamID"] ?>" />
<?php showrow($row, $recid) ?>
<strong>Are you sure you want to delete this record? </strong><div class="btn-group"><input class="btn btn-primary" type="submit" name="Delete" value="Yes" /> <input class="btn btn-default" type="button" name="Ignore" value="No" onclick="javascript:history.go(-1)" /></div>
</form>
<?php
db_free_result($res);
}
?>

<?php
function sql_select(){
global $conn;
global $filter;
global $filterfield;

$filterstr = isset($filter) ? "%". $filter ."%" : "";	
$sql = "SELECT * FROM `".DB_PREFIX."exams`";	
if(isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
$sql .= " WHERE ". secure_string($filterfield) ." LIKE '". secure_string($filterstr) ."'";
}
$res = db_query($sql,DB_NAME,$conn);
return $res;
}

function sql_getrecordcount(){
global $conn;
global $filter;
global $filterfield;

$filterstr = isset($filter) ? "%". $filter ."%" : "";	
$sql = "SELECT COUNT(*) FROM `".DB_PREFIX."exams`";
if(isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
$sql .= " WHERE ". secure_string($filterfield) ." LIKE '". secure_string($filterstr) ."'";
}
$res = db_query($sql,DB_NAME,$conn);
$row = db_fetch_array($res);
reset($row);
return current($row);
}
//INSERT NEW EXAM
function sql_insert($FIELDS){
global $conn;

$sql = sprintf("INSERT INTO `".DB_PREFIX."exams`(`ExamID`, `ExamName`, `ExamCourse`, `ExamUnit`, `ExamInstructions`, `ExamDate`, `ExamDeadline`, `ExamDuration`) VALUES (%d,'%s','%s','%s','%s','%s','%s','%s')", $FIELDS['ExamID'], $FIELDS['ExamName'], $FIELDS['ExamCourse'], $FIELDS['ExamUnit'], $FIELDS['ExamInstructions'], $FIELDS['ExamDate'], $FIELDS['ExamDeadline'], $FIELDS['ExamDuration']);	
db_query($sql,DB_NAME,$conn);

if(db_affected_rows($conn)>0){
    return true;
}else{	
    return false;
}
}
// UPDATE EXAM DETAILS AFTER EDIT
function sql_update($FIELDS){
global $conn;
$sql = sprintf("UPDATE `".DB_PREFIX."exams` SET `ExamName` = '%s',`ExamCourse` = '%s',`ExamUnit` = '%s',`ExamInstructions` = '%s',`ExamDate` = '%s', `ExamDeadline` = '%s',`ExamDuration` = '%s' WHERE " .primarykeycondition(). "", $FIELDS['ExamName'], $FIELDS['ExamCourse'], $FIELDS['ExamUnit'], $FIELDS['ExamInstructions'], $FIELDS['ExamDate'], $FIELDS['ExamDeadline'], $FIELDS['ExamDuration']);
db_query($sql,DB_NAME,$conn);

if(db_affected_rows($conn)>0){		
    return true;
}else{
    return false;
}
}
//ACTIVATE STUDENT EXAM
function activate_student_exam($ExamID, $StudentID){
global $conn;
$sql = sprintf("UPDATE `".DB_PREFIX."students` SET `disabledFlag` = %d WHERE `UID` = %d LIMIT 1", $disabledFlag, $editID);
db_query($sql,DB_NAME,$conn);

if(db_affected_rows($conn)>0){
    $_SESSION['MSG'] = ConfirmMessage("Student has been updated successfully.");
}else{
    $_SESSION['MSG'] = WarnMessage("No changes made!");
}
redirect("admin.php?dispatcher=exams");
}
//UPDATE EXAM TO STUDENTS IN THAT UNIT
function sql_update_status($disabledFlag, $editID){
	global $conn;
	
	//Update student
	$sql = sprintf("UPDATE `".DB_PREFIX."exams` SET `disabledFlag` = %d WHERE `ExamID` = %d", $disabledFlag, $editID);
	db_query($sql,DB_NAME,$conn);
	if(db_affected_rows($conn)>0){
		if($disabledFlag ==0){
            $_SESSION['MSG'] = ConfirmMessage("Exam has been opened to all students enrolled in this Unit.");
        }else{
            $_SESSION['MSG'] = WarnMessage("Exam has been Closed to all students enrolled in this Unit.");
        }
	}else{
		$_SESSION['MSG'] = WarnMessage("No changes made!");
	}
	redirect("admin.php?dispatcher=exams");
}
// DELETE AN EXAM = NOT REALLY, JUST HIDE.
function sql_delete(){
global $conn;

$sql = "UPDATE `".DB_PREFIX."exams` SET `deletedFlag` = 1 WHERE " .primarykeycondition();
db_query($sql,DB_NAME,$conn);

if(db_affected_rows($conn)>0){
    $_SESSION['MSG'] = ConfirmMessage("Exam has been deleted successfully");
}else{
    $_SESSION['MSG'] = ErrorMessage("Failed to delete selected Exam. Please try again later...");
}
redirect("admin.php?dispatcher=exams");
}
//PREPARE UPDATE CONDITION
function primarykeycondition(){

$pk = "";
$pk .= "(`ExamID`";
if (@$_POST["eid"] == "") {
    $pk .= " IS NULL";
}else{
    $pk .= " = " .intval(@$_POST["eid"]);
};
$pk .= ")";
return $pk;
}
//MANAGE EXAM QUESTIONS
function manage_exam_questions(){
	global $conn;
	?>
	    <ol class="breadcrumb"><li><a href="admin.php" title="Dashboard">Dashboard</a></li><li><a href="admin.php?dispatcher=exams">Exams</a></li><li class="active">Exam Questions</li></ol>
		<p>This list shows all questions in the system for all active exams.</p>
		<form name="announcements" method="post" action="#tabs-2">
		<div id="hideMsg"><?php if(isset($_SESSION['MSG'])) echo $_SESSION['MSG'];?></div>
		<table width="100%" class="display table table-striped table-bordered table-hover">				
		<thead>
		<tr>
		<th>#</th>
		<th>Question</th>
		<th>Exam</th>
        <th>Question Type</th>
		<th>System Answer</th>
		<th>Marking</th>
		<th>Credits</th>
		<th class="no-sort">Actions</th>
		</tr>
        </thead>
		<?php
		$AllQuestions = sprintf("SELECT * FROM `".DB_PREFIX."exam_questions` WHERE `disabledFlag` = %d AND `deletedFlag` = %d", 0, 0);
		//run the query
		$result = db_query($AllQuestions,DB_NAME,$conn);
		if(db_num_rows($result)>0){
		  echo "<tbody>";
		  while($AllQ = db_fetch_array($result)){
              if($AllQ['AnswerOption'] =="Z"){
                $AllQ['AnswerOption'] = "None System marking";
              }
			  echo "<tr>
              <td>".$AllQ['QuestionID']."</td>
              <td>".decode($AllQ['Question'])."</td>
			  <td>".getExamName($AllQ['ExamID'])."</td>
              <td>".$AllQ['QuestionType']."</td>
              <td>".$AllQ['AnswerOption']."</td>
              <td>".$AllQ['SystemMarked']."</td>
              <td>".$AllQ['Credits']."</td>"; 
              ?>
			  <td>
              <a href=""><i class="fa fa-edit"></i></a><br>
              <a href=""><i class="fa fa-eye-slash"></i></a> <br>
              <a href=""><i class="fa fa-trash"></i></a>
              </td>
			  </tr>
        <?php		
		  }
		  echo "</tbody>";
		  ?>
		  <tfoot>
		  <tr>
		  <td colspan="7" align="right">
          </td>
		  </tr>
		  </tfoot>
		  <?php
		}
		?>
		</table>
		</form>
		<?php
		unset($_SESSION['MSG']);
	}
//SHOW EXAM SCORE FOR ALL STUDENTS ALL EXAMS
function preview(){
    Global $conn;
    global $to_preview;
    $test = array();
if(isset($_POST['preview'])){
    $ExamID = $_POST['ExamName'];
    $to_preview['Question'] = array();
    $to_preview['Question']['Options'] = array();
        //get questions for this paper
$resQuestions = getExamQuestions($ExamID);			
if(db_num_rows($resQuestions)>0){	
    $loop = 0;			
    while($q = db_fetch_array($resQuestions)){
    array_push($to_preview['Question'], $q);//create array of questions
    }
}

}
?>
<br><br>
<form class= "form-inline" action ="" method ="post">
<div class="form-group">
<label for="">Select Exam to preview: <span class="text-danger">*</span></label>
<?php echo sqlOption("SELECT `ExamID`,`ExamName` FROM `".DB_PREFIX."exams` WHERE `deletedFlag` = 0","ExamName","","--Select exam--");?>
<span class="text-danger"><?=$ERRORS['ExamName'];?></span>
</div>
<div class="form-group">
<input type="submit" class ="form-control" value="Preview" name ="preview"/>
</div>
</form>
<?php if(!empty($to_preview)){
//    echo "<pre>";
//  print_r($to_preview) ;
// // //print_r($to_preview['Question']['Options']);
//  echo "</pre>"
    ?>
<div id="page-wrap">
<h1><?php echo getExamName($to_preview['Question'][0]['ExamID']); ?></h1>
<h3><?php echo "Instructions"; ?></h3>
<span><em>Take note of the instructions below</em></span><br>
<ul>
<li>
<?php echo getExamInstructions($to_preview['Question'][0]['ExamID']); ?></li>
<li>
<?php echo "You will have exactly ".getExamDuration($to_preview['Question'][0]['ExamID'])." minutes to complete this exam"; ?></li>
<li>
You will not be allowed to go back once you start your exam. Make sure you dont refresh the page until you are done.
</li>
</ul>

<ol>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="examID">
<?php 
foreach($to_preview['Question'] as $single_question): 
if(!empty(decode($single_question['Question']))){
?>
<li>
    <h3><?php echo decode($single_question['Question']); ?></h3>
    <input type="text" name ="QuestionID" value ="<?php echo $single_question['QuestionID']; ?>" hidden ="hidden"/>
<?php 
   //question options 
   if($single_question['QuestionType'] != "Closed"){?>
    <textarea name ="Answer" class="form-control tinymce" value =""></textarea>
<?php   }else{
 if(db_num_rows(db_query(sprintf("SELECT * FROM `".DB_PREFIX."exam_question_options` WHERE `disabledFlag` = %d AND `deletedFlag` = %d AND QuestionID = %d", 0, 0, $single_question['QuestionID']),DB_NAME,$conn))>0){
     $options_list = array();
     $options_result = db_query(sprintf("SELECT * FROM `".DB_PREFIX."exam_question_options` WHERE `disabledFlag` = %d AND `deletedFlag` = %d AND QuestionID = %d", 0, 0, $single_question['QuestionID']),DB_NAME,$conn);
     while($row = db_fetch_array($options_result)){
?>
    <div>
        <input type="radio" name="answers<?php echo $single_question['QuestionID']; ?>" id="question-1-answers-A" value="<?php echo $row['OptionIdentity']; ?>" />
        <label for="question-1-answers-A"><?php echo $row['OptionIdentity'].") ".$row['OptionName'];?></label>
    </div>
    </li>
<?php
  }
}
} //end question type else
}
echo "<br><br>";
endforeach;
    ?>
<input type="submit" value="Send for Marking" />
</form>
</ol>

</div>
<?php
}else{
    echo "This paper has no questions set yet";
} 
}
?>