<script type="text/javascript">
	$(document).ready(function(){
		$("#btndown").click(function() {
			$('html, body').animate({
				scrollTop: $("#btndown").offset().top
			}, 800);
		});
		$(window).scroll(function(){
			if ($(this).scrollTop() > 100) {
				$("#btntop").fadeIn();
			} else {
				$("#btntop").fadeOut();
			}
		});

		$("#btntop").click(function(){
			$('html, body').animate({scrollTop : 0},800);
			return false;
		});
	});
	//get completed students
	function completedStudent(idmateri,limit,offset){
		$('#finishedcourse').html('loading...');
		url = '<?php echo site_url("course/studentCompletingMateri");?>';
		$.ajax({
			url:url,
			type:'post',
			data:{limit:limit,offset:offset,idmateri:idmateri},
			success:function(response){
				//alert(response.length);
				if(response.length<10){
					$('#finishedcourse').html('<h4>student not found</h4>');
				}else{
					$('#finishedcourse').html(response);
					$('#studentloader').hide();
				}
				
			},
			error:function(){
				alert('something wrong, please refresh page');
				$('#finishedcourse').html('cannot show data');
			}
		});
	}
	//choose rewind language
	function rewindLanguage(action){
		//load modal
		 $("#rewindForm").attr("action",action);
		$('#rewindModal').foundation('reveal', 'open');
	}
</script>
<a class="button" style="display:none;padding:10px;position: fixed;right: 0;bottom: 0;" id="btntop"><span style="font-size:2rem"; class="fi-arrow-up"></span></a>

<?php
//sount materi completion
$totalnow = $this->m_course->countCourseStepByMateri($detCourse['id_materi'],$detCourse['id_level'],$detCourse['id_course']);
$totalCourse = $this->m_course->countCourseByMateri($detCourse['id_materi']);
$recentPercentage = number_format(($totalnow*100/$totalCourse),1);
?>
<?php
					//encode id user course
$encIdUserCourse = base64_encode(base64_encode($detCourse['id_user_course']));
$encIdUserCourse = str_replace('=', '', $encIdUserCourse);
?>
<section id="title">
	<center>
		<br/>
		<?php
		if(!empty($materi['logo'])){$logo = base_url('assets/img/logo/'.$materi['logo']);}
		else{$logo = base_url('assets/img/logo/other logo.png'); }
		echo '<img src="'.$logo.'"/>'
		?>
		<h1 style="margin:0"><?php echo $materi['title'];?> / <?php echo $recentPercentage;?>%</h1>
		<p><?php echo $materi['description'];?></p>
		<div id="progressanimate" style="height:10px;width:50%" class="radius progress">
			<span style="float:left;color:#fff;width:<?php echo $recentPercentage;?>%;" class="meter"></span>
		</div>
		<br/>
		<p style="margin:0">Active Student <strong><?php echo $this->m_course->countStudentByMateri($materi['id_materi'],'incomplete')?></strong></p>
		<p style="margin:0">Completed Student <strong><?php echo $this->m_course->countStudentByMateri($materi['id_materi'],'completed')?></strong></p>
		<br/>
		<div class="row">
			<form class="large-6 large-offset-3 columns" method="GET" action="<?php echo site_url('course/start/'.$encIdUserCourse)?>" class="button large">
				<select style="height:45px;color:gray" class="large-6 columns" name="lang">
					<option value="en">English</option>
					<option value="id">Indonesia</option>
				</select>
				<button class="button button-lg large-6 columns" type="submit">Resume <i class="fi-arrow-right"></i></button>
			</form>
		</div>
	</center>
</section>
<?php 
$mytime =  $detuserCourse['finishtime'];
$mytime = json_decode($mytime,true);//json to array
?>
<section>
	<center>		
		<div class="row">			
			<div class="large-8 collapse" columns>				
				<!-- skill completion -->
				<div class="row">
					<dl style="border-top:1px solid #E8E8E8" class="tabs" data-tab>
						<dd style="width:33.333333333%" class="active"><a href="#mycourse">Review</a></dd>
						<dd style="width:33.333333333%"><a href="#badge">Badges</a></dd>
						<dd style="width:33.333333333%"><a href="#finishedcourse" onclick="completedStudent(<?php echo $materi['id_materi'];?>,20,0)">Finished Student</a></dd>
					</dl>
					<br/>
					<div class="tabs-content">
						<div class="content active" id="mycourse">
							<!-- level and review list -->
							<?php
							echo 'Course Begin : '. date('d M Y H:i:s', strtotime($detCourse['startdate']));
							$today = date_create(date('Y-m-d'));
							$last = date_create(date('Y-m-d', strtotime($detCourse['lastdate'])));
							$diff=date_diff($last,$today);
							if($diff->y != 0){
								$log = $diff->y.' Years Ago';
							}else if($diff->m != 0){
								$log = $diff->m.' Months Ago';
							}else if($diff->d != 0){
								$log = $diff->d.' Days Ago';
							} else {
								$log = 'Today';
							}
							?>
							<p style="margin:0"><strong>Course Has Been Start <?php echo $log;?>  </strong></p> 
							<hr/>
							<?php foreach ($level as $l):
							$totalnow = $this->m_course->countCourseStepByLevel($recentCourseStep,$l['id_level']);
							$totalCourse = $this->m_course->countCourseByLevel($l['id_level']);
							$recentPercentage = number_format(($totalnow*100/$totalCourse),1);
							?>
							<p style="margin:0"><strong><?php echo 'Level '.$l['level'].'</strong> : '.$l['title']?> / <?php echo $recentPercentage;?>%</p>
							<p style="margin:0;color:gray"><?php echo $l['description']?></p>
							<div  style="height:10px" class="radius progress">
								<span style="float:left;color:#fff;width:<?php echo $recentPercentage;?>%;" class="meter"></span>
							</div>
							<br/><br/>
							<table>
								<tr>
									<th style="width:70%">Course</th>
									<th style="width:10%">Estimate</th>
									<th style="width:10%">Goal</th>
									<th style="width:10%">Status</th>
								</tr>
								<?php
									$course = $this->m_course->courseByLevel($l['id_level']);//show course by level
									foreach($course as $c):?>
									<tr>
										<td><?php echo $c['title'];?>
											<?php if($c['id_level'] < $detCourse['id_level'] || ($c['step'] <= $recentCourseStep && $c['level'] <= $detCourse['level'])){//if level n course step <= now = completed
												echo '<a onclick="rewindLanguage(\''.site_url('course/rewind/'.str_replace('=','', base64_encode(base64_encode($c['id_course']))).'/'.$this->uri->segment(3)).'\')"  style="background-color:#008cba;color:#fff;padding:2px;font-size:9px">rewind</a>';
											}?>
											<br/><small style="color:gray"><?php echo $c['description']?></small></td>
											<td><?php echo $c['estimate'].'m'; ?></td>
											<td>
												<?php 
											if($c['id_level'] < $detCourse['id_level'] || ($c['step'] <= $recentCourseStep && $c['level'] <= $detCourse['level'])){//if level n course step <= now = completed
												if($mytime[$c['id_course']] <= $c['estimate']){//green
													$time = $c['estimate']-$mytime[$c['id_course']];
													echo '<span style="font-weight:bold;color:green"> -'.$time.'m</span>';
												}else{//red
													$time = $mytime[$c['id_course']]-$c['estimate'];
													echo '<span style="font-weight:bold;color:red"> +'.$time.'m</span>';
												}
											} else {
												echo '';
											}
											?>
										</td>
										<td>
											<?php 
											if($c['id_level'] < $detCourse['id_level'] || ($c['step'] <= $recentCourseStep && $c['level'] <= $detCourse['level'])){//if level n course step <= now = completed
												echo '<span style="color:green" class="fi-check"></span>';
											} else {
												echo '<span style="color:red" class="fi-x"></span>';
											}
											?>
										</td>
									</tr>
									<?php
									endforeach;
									?>
								</table>
								<br/><br/>
							<?php endforeach ?>
							<!-- end of review list-->
						</div>
						<!-- <div class="content" id="finishedcourse"></div>					   -->
						<form class="" method="GET" action="<?php echo site_url('course/start/'.$encIdUserCourse)?>" class="button large">
							<select style="height:45px" class="large-6 columns" name="lang">
								<option value="en">English</option>
								<option value="id">Indonesia</option>
							</select>
							<button class="button button-lg large-6 columns" type="submit">Resume <i class="fi-arrow-right"></i></button>
						</form>
						<div class="content" id="badge">
							<?php if(empty($materibadge) && empty($allbadge)){ echo '<center><h3>you don\'t have any badge</h3></center>';}else{?>
							<?php foreach($allbadge as $ab):?>
								<span><img data-tooltip aria-haspopup="true" title="<?php echo $ab['description'];?>" style="width:50px" src="<?php echo base_url('assets/img/badge/'.$ab['logo']) ?>"></span>
							<?php endforeach; ?>
							<?php foreach($materibadge as $mb):?>
								<span><img data-tooltip aria-haspopup="true" title="<?php echo $mb['description'];?>" style="width:50px" src="<?php echo base_url('assets/img/badge/'.$mb['logo']) ?>"></span>
							<?php endforeach; ?>
							<?php } //end of else?>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</center>
</section>
<!-- end of syllabus list-->
<!-- modal to choose language -->
<div id="rewindModal" class="tiny reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<h4 id="modalTitle">Choose Language</h4>
	<form id="rewindForm" class="" method="GET" action="" class="button large">
		<select style="height:45px" class="large-6 columns" name="lang">
			<option value="en">English</option>
			<option value="id">Indonesia</option>
		</select>
		<button class="button button-lg large-6 columns" type="submit">Resume <i class="fi-arrow-right"></i></button>
	</form>
	<a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<!-- end of modal to choose language -->