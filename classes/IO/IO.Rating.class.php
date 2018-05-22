<?php 
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IORating: rate lesson content/tutor after every lesson
 *
 * @author I O Juma
 */
class IORating{
    protected $student;
    protected $unit;
    protected $lesson;
    protected $modal;
    protected $db;
    protected $conn;


    function __construct($student = Null, $unit = Null, $lesson = Null, $db = Null, $conn = Null) {
        $this->student = $student;
        $this->unit = $unit;
        $this->lesson = $lesson;
        $this->modal = $modal;
        $this->db = $db;
        $this->conn = $conn;
    }

    public function CeateSurveyModal(){
        $modal = '
        <!-- Modal -->
        <div class="modal fade" id="rate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Rate Lesson</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>

            <div class="modal-body">
            '.$this->ShowSurvey().'
            </div>

        </div>
        </div>
        </div>';

        return $modal;
    }
    public function LaunchModal($title){
        $md = $this->modal;
        $launch = '
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rate">
        '.$title.'
        </button>'  ;
        return $launch; 
    }
    public function SurveyWizardHead(){
        return '<div class="wizard-inner">
        <div class="connecting-line"></div>
        </div>';
    }
    public function prepareSurveyQuestions(){
        if(!is_array(SearchDB())){
            return 'No survey could be loaded.';
        }
        $questions = '<p>'.$this->StarRate().'</p><hr>';
        $count = 0;
        $active = '';
        foreach(SearchDB() as $q):
            if($count == 0){
                $active = 'active';
            }else{
                $active = '';
            }
            $questions .= '<h5 style="color:#218fea;"><b>'.$q['Question'].'</b></h5>';
            $questions .= $this->SuveryQuestionOptions($q['Options'],$q['Icons'], $q['ID']);
            $questions .= '<br>';
            $questions .= '
            <label for="exampleFormControlInput1">Write a complement</label>
            <input type="text" class="form-control" name="why'.$q['ID'].'" required="required">';
        endforeach;
    return $questions;
    }
    public function SuveryQuestionOptions($options, $icons, $Qid){
            $Qoptions = '';
            if(empty($options)){
                return 'There were no choices found.';
            }
        $options_arr = explode(",", $options);
        $icons_arr = explode(",", $icons);
        $Qoptions .= '';
        $c = 0;
         foreach ($options_arr as $o):
            $Qoptions .= '
            <label class="btn btn-primary"><img src="'.$icons_arr[$c].'" alt="..." class="img-thumbnail img-check">
            <input type="checkbox" name="choice'.$Qid.'[]" id="item4" value="'.$o.'" class="hidden" autocomplete="off" required="required"><br>'.$o.'</label>
            ';
            $c++;
         endforeach;
         $Qoptions .= ''  ;

         return $Qoptions;
    }
    public function StarRate(){
        $rating = '
                <h3 style="color:#218fea;">On a scale of 1-5, rate this lesson</h3>
                <div class="rating">
                <span><input type="radio" name="rating" id="str5" value="5"><label for="str5"></label></span>
                <span><input type="radio" name="rating" id="str4" value="4"><label for="str4"></label></span>
                <span><input type="radio" name="rating" id="str3" value="3"><label for="str3"></label></span>
                <span><input type="radio" name="rating" id="str2" value="2"><label for="str2"></label></span>
                <span><input type="radio" name="rating" id="str1" value="1"><label for="str1"></label></span>
                </div><br>';
        return $rating;
    }

    public function ShowSurvey(){
       if(isset($_POST['finish'])){
           //send rating in db
       }else{
           //show form
        $form = '';
        $form .= '
        <div class="container">
            <div class="row">
                    <div class="wizard">
                    '.$this->SurveyWizardHead().'
                        <form role="form" action="" method="post">
                            <div class="tab-content" style="width:40%;">
                                '.$this->prepareSurveyQuestions().'
                                    <p><br>
                                    <button type="submit" name="finish" class="btn btn-primary">Finish</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Not Now</button></p>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
            </div>
        </div>';
    return $form;
       }
       return null;
    }

}
?>