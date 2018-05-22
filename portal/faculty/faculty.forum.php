<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty Portal | Forum Board";
//-->
</script>

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Forum Board</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    
		<div class="ForumDetails">
		  <h3>Forum Categories</h3>
			<p class="text-right"><a href="?dispatcher=forum&view=category&task=add" class="btn btn-primary">Add New Category</a></p>
			<?php
			//Get requested task
      $view = isset($_GET['view'])?$_GET['view']:"";
			$task = isset($_GET['task'])?$_GET['task']:"";
      
      $view = strtolower($view);
      switch($view) {
				case "category":
					if($task == "add"){
						
					}
				break;
				default:
					$sqlGetCategories = "SELECT * FROM `".DB_PREFIX."forum_categories`";
					
					//Execute the query or die if there is a problem
					$resultGetCategories = db_query($sqlGetCategories,DB_NAME,$conn);
					 
					if(!$resultGetCategories){
						echo 'The categories could not be displayed, please try again later.';
					}
					else{
						if(db_num_rows($resultGetCategories) == 0){
							echo 'No categories defined yet. <a href="?dispatcher=forum&view=category&task=add" class="btn btn-sm">Create One</a>';
						}
						else{
							//prepare the table
							echo '<table class="display table table-striped table-bordered table-hover">
										<thead>
										<tr>
											<th>Category</th>
											<th>Topics</th>
											<th>Replies</th>
										</tr>
										<thead>
										<tbody>';								 
									 
							while($row = db_fetch_array($resultGetCategories)){               
								echo '<tr>';
								echo '<td>';
								echo '<span class="lead"><a href="?dispatcher=forum&view=category&id">' . $row['cat_name'] . '</a></span><br>' . $row['cat_description'];
								echo '</td>';
								echo '<td><a href="?dispatcher=forum&view=topic&id=">0</a></td>';
								echo '<td><a href="?dispatcher=forum&view=replies&id=">0</a></td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
						}
					}
				break;
			}
			?>
		</div>
		
  </div>
</div>
<!-- /.row -->