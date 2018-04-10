<script>
<!--//
//Define page title
document.title = "<?=SYSTEM_SHORT_NAME?> - Faculty | My Calendar";

$(function() {
	
	//$('#calendar').fullCalendar('option', 'timezone', 'Africa/Nairobi' || false);
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,listWeek,listDay'
		},
		defaultView: 'month',
		navLinks: true, // can click day/week names to navigate views
		eventLimit: true, // allow "more" link when too many events
		// customize the button names,
		// otherwise they'd all just say "list"
		views: {
			month: { buttonText: 'Month' },
			listDay: { buttonText: 'List Day' },
			listWeek: { buttonText: 'List Week' }
		},
		events: [
			<?php echo getCalendarEvents('Faculty'); ?>
		]
	});
	
});
//-->
</script>
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">My Calendar</h1>
  </div>
  <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
  <div class="col-lg-12">
    <div class="cms-contents-grey">
			<!--Begin Forms-->        
			<div id="calendar"></div>
			<!--End Forms-->
	</div>
  </div>
</div>
<!-- /.row -->