<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | Assignments";

$(document).ready(function() {
	//Load TinyMCE	
	tinymce.init({		
		selector: 'textarea.tinymce',
		height: 100,
		theme: 'modern',
		plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help',
		toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat',
		image_advtab: true,
		content_css: "<?=SYSTEM_URL;?>/styles/tinymce.editor.css"
	});
	
	//$('#collapse1').addClass('in');
	
});
//-->
</script>
<style>
@import url("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
@import url('https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700');
@import url('https://fonts.googleapis.com/css?family=Slabo+27px');
@import url('https://fonts.googleapis.com/css?family=Libre+Baskerville:400,700');

a, a:hover, a:focus{outline:none; text-decoration:none;}
body{
    font-family: 'Open Sans', sans-serif;
    overflow-x: hidden;
}
.card {
    -moz-box-direction: normal;
    -moz-box-orient: vertical;
    background-color: #fff;
    border-radius: 0.25rem;
    display: flex;
    flex-direction: column;
    position: relative;
    margin-bottom:1px;
    border:none;
}
.card-header:first-child {
    border-radius: 0;
}
.card-header {
    background-color: #f7f7f9;
    margin-bottom: 0;
    padding: 20px 1.25rem;
    border:none;
    
}
.card-header a i{
    float:left;
    font-size:25px;
    padding:5px 0;
    margin:0 25px 0 0px;
    color:#195C9D;
}
.card-header i{
    float:right;        
    font-size:30px;
    width:1%;
    margin-top:8px;
    margin-right:10px;
}
.card-header a{
    width:97%;
    float:left;
    color:#565656;
}
.card-header p{
    margin:0;
}

.card-header h3{
    margin:0 0 0px;
    font-size:20px;
    font-family: 'Slabo 27px', serif;
    font-weight:bold;
    color:#3fc199;
}
.card-block {
    -moz-box-flex: 1;
    flex: 1 1 auto;
    padding: 20px;
    color:#232323;
    box-shadow:inset 0px 4px 5px rgba(0,0,0,0.1);
    border-top:1px soild #000;
    border-radius:0;
}
</style>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Exams Awaiting Marking</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
      <!--Begin Forms-->        
<?php
    //Required
    require_once("$class_dir/EvarsitySMS.php");
    ?>
    <h3>My Exams</h3>
    <?php
    $do = !empty($_GET['task'])?$_GET['task']:"";
    //update score
    if(isset($_POST['ty'])){
      $exam = secure_string($_POST['exam']);
      $student = secure_string($_POST['student']);
      $score = secure_string($_POST['score']);
      //arrays
      $question = secure_string($_POST['question']);
      $credits = secure_string($_POST['credits']);
      $comment = secure_string($_POST['c']);
      //final score
      $sc = array_sum($credits) + $score;
      $openq = array_sum($credits);
      $path = '?tab=10&task=view&paper='.$exam;
      $params = array($exam, $student, $sc, $openq, $path);
      //combine all
      $result = array_map(function ($q, $c, $co) {
        return array_combine(
          ['Q', 'c', 'co'],
          [$q, $c, $co]
        );
      }, $question, $credits, $comment);
      $arr = json_encode($result, JSON_PRETTY_PRINT);

      updateScore($params, $arr);
    }
    switch($do){
      case '':
    if(!empty(getTutorUnits($faculty['FacultyID']))){
        // foreach( getTutorUnits($faculty['FacultyID']) as $u):
        //     echo $u['UnitID'];
        // endforeach;
       // echo '<pre>';
       //print_r(getTutorUnits($faculty['FacultyID']));  
       foreach( getTutorUnits($faculty['FacultyID']) as $u):
        if(!empty($u)){
          ?>
          <!-- table begin -->
    <table width="100%" class="display table table-striped table-bordered table-hover">
		  <thead>
		  <tr>
            <th>ExamID</th>
            <th>Exam</th>
            <th>Exam Course</th>
            <th>Exam Unit</th>
            <th>Exam Date</th>
            <th>Exam Deadline</th>
            <th>Exam Duration</th>
            <th>Actions</th>
		  </tr>
		  </thead>
		  <tbody>
          <?php
          foreach( $u as $nobody ):
          echo '<tr>';
          echo '<td>'.$nobody['ExamID'].'</td>';
          echo '<td>'.getFacultyExamsDetails($nobody['ExamID'])[0]['ExamName'].'</td>';
          echo '<td>'.getFacultyExamsDetails($nobody['ExamID'])[0]['ExamCourse'].'</td>';
          echo '<td>'.getFacultyExamsDetails($nobody['ExamID'])[0]['ExamUnit'].'</td>';
          echo '<td>'.getFacultyExamsDetails($nobody['ExamID'])[0]['ExamDate'].'</td>';
          echo '<td>'.getFacultyExamsDetails($nobody['ExamID'])[0]['ExamDeadline'].'</td>';
          echo '<td>'.getFacultyExamsDetails($nobody['ExamID'])[0]['ExamDuration'].'</td>';
          echo '<td><a href="?tab=10&task=view&paper='.$nobody['ExamID'].'">Open</a></td>';
          echo '</tr>';
          endforeach;
          echo '
          </tbody>
          </table>';
        }
       endforeach;
      // echo '</pre>';
        //print_r(getTutorExams("COT003")) ;
    }else{
    echo '<p>You do not have any exams to mark</p>';
    }
    break;
    case 'view':
    $paper = !empty($_GET['paper'])?$_GET['paper']:"";
    if( !empty( $paper )) {
      //get students who have sat for this exam and their answers for the open questions
?>
<div class="container">
<div class="row">
<div class="col-md-10 offset-2">
<div class="bd-example" data-example-id="">
<div id="accordion" role="tablist" aria-multiselectable="true">
<?php
$cc = 0;
//print_r(getStudentSatforExam($paper));
foreach( getStudentSatforExam($paper) as $each):
  //print_r(sanitizeJson($each['unmarked']));
?>
  <div class="card">
    <div class="card-header" role="tab" id="headingOne">
      <div class="mb-0">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $cc; ?>" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
          <i class="fa fa-user-o" aria-hidden="true"></i>
            <h3><?php echo getStudentData($each['StudentID'])['FName'].' '.getStudentData($each['StudentID'])['LName'].'('.$each['StudentID'].')'; ?></h3>
            <p>Paper: <?php echo getExamName($paper); ?>
            <?php echo checkMarked($each['StudentID'], $paper); ?></p>
        </a>
        <i class="fa fa-angle-right" aria-hidden="true"></i>
      </div>
    </div>

    <div id="collapse<?php echo $cc; ?>" class="collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="">
      <div class="card-block">
        <?php
        $crt = 0;
        $answers = sanitizeJson(decode($each['unmarked']));
        if($each['IsMarked'] != ''){
          foreach( $answers as $a):
            $crt = $crt + decode(getQuestionName($paper,$a['Question'])[0]['Credits']);
          endforeach;
            echo 'This is marked. Student Scored: <b> '.$each['IsMarked'].'/'.number_format($crt, 0).'</b>';
        } 
        // if($each['IsMarked'] == '') {
          //print student answers for this paper and a form to allocate marks and coments
         // $questions = getQuestionName();
          ?>
          <form action = "" method ="post">
          <?php 
             if(!empty($answers)){
               $index = 0;
              foreach( $answers as $a):
              $crt = decode(getQuestionName($paper,$a['Question'])[0]['Credits']);
              $ans = $a['StudentAnswer'];
              $ans = str_replace("rn", "", $ans);
              if(!empty($ans)){
                $ans = '<b style="color:#f7981d;"><u>'.getStudentData($each['StudentID'])['FName'].' Wrote:</u></b> <div class="alert alert-primary" role="alert">'.$ans.'</div>';
              }else{
                $ans = '<b style="color:#f7981d;"><u>'.getStudentData($each['StudentID'])['FName'].'</u> did not attempt this question</b>';
              }
          ?>
          <div class="row">
            <div class="form-group col-sm-10">
                <label for="marking" class="">Q:<?php echo $a['Question'];?> <?php echo decode(getQuestionName($paper,$a['Question'])[0]['Question']);?><abbr class="text-danger" title="required"></abbr></label>
            </div>
            <div class="form-group col-sm-2">
                <label for="credits" style="color:#337ab7;"><?php echo decode(getQuestionName($paper,$a['Question'])[0]['Credits']);?> marks(credits)<abbr class="text-danger" title="required"></abbr></label>
            </div>
            <div class="form-group col-sm-12">
                <label for="credits" style="clor:Orange;">Student Ans.
                <i><?php echo $ans; ?></i>
                </label>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-sm-12">
                <label for="credits" class="">Allocate Credits.<br>
                </label>
              <input type="text" min="0" max="<?php echo decode(getQuestionName($paper,$a['Question'])[0]['Credits']); ?>" class="form-control required" name="credits[<?php echo $index;?>]" id="credits" value="<?php echo $each['IsMarked']; ?>">
              <input type="text" name="exam" id="exam" value="<?php echo $paper; ?>" hidden="hidden">
              <input type="text" name="student" id="student" value="<?php echo $each['StudentID']; ?>" hidden="hidden">
              <input type="text" name="question[<?php echo $index;?>]" id="q" value="<?php echo $a['Question']; ?>" hidden="hidden">
              <input type="text" name="score" id="score" value="<?php echo $each['ExamScore']; ?>" hidden="hidden">
            </div>
          </div>
          <div class="row">
            <div class="form-group col-sm-12">
                <label for="credits" class="">Lecturer Comment.<br></label>
                <textarea class="form-control tinymce" name="c[<?php echo $index;?>]" rows="2"></textarea>
            </div>
        </div>
          <?php 
          $index++;
               endforeach;
              }
          ?>
          <div class="form-group col-sm-12">
            <input type="submit" class="btn btn-primary" name="ty" value="Update Score" />
          </div>
          </form>
       
        <?php
     // }
      ?>
      </div>
    </div>
  </div>
<?php 
$cc ++;
endforeach; ?>
</div>
</div>
</div>
</div>
</div>
<?php
    }
    break;
    case 'mark':
    break;
  }
?>
      <!--End Forms-->
	</div>
  </div>
</div>
<!-- /.row -->