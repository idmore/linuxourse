<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript">
	function showmarkdown(){
		$('#markdown').toggle('fast');
	}
	function addLike(idcomment){
		iduser = <?php echo $this->session->userdata['student_login']['id_user'];?>;//get id user
		url = '<?php echo site_url("discussion/commentAction")?>';
		give='up';
		$.ajax({
			url:url,
			type:'post',
			data:{idcomment:idcomment,iduser:iduser,give:give},
			success:function(response){
				updateCount('up',idcomment);
				updateCount('down',idcomment);
			},
			error:function(){
				alert('something wrong, please refresh page');
			}
		});
	}
	function addDislike(idcomment){
		iduser = <?php echo $this->session->userdata['student_login']['id_user'];?>;//get id user
		url = '<?php echo site_url("discussion/commentAction")?>';
		give='down';
		$.ajax({
			url:url,
			type:'post',
			data:{idcomment:idcomment,iduser:iduser,give:give},
			success:function(response){
				updateCount('up',idcomment);
				updateCount('down',idcomment);
			},
			error:function(){
				alert('something wrong, please refresh page');
			}
		});
	}
	function updateCount(give,idcomment){		
		url = '<?php echo site_url("discussion/updateCount")?>';
		$.ajax({
			url:url,
			type:'post',
			data:{idcomment:idcomment,give:give},
			success:function(response){
				if(give=='up'){
					$("#like-"+idcomment).html(response);
				}else{
					$("#dislike-"+idcomment).html(response);
				}
			},
			error:function(){
				return alert('something wrong, refresh page');
			}
		});
	}
</script>
<section id="title">
	<center>		
		<h2 style="margin:0">linuXourse Discussion</h2>
		<p>have problem, stuck, something not working, discuss it</p>
	</center>
</section>
<br/>
<div class="row">	
	<?php $this->load->view('discussion/order')?>
	<br/><br/>
	<div class="large-8 columns">
		<div style="float:left;padding:0.9375rem" class="discuss-item large-12 columns">
			<?php
			if(!empty($view['pp'])){
				$avatar = base_url('assets/img/avatar/'.$view['pp']);
			}else{
				$avatar = base_url('assets/img/avatar.png');
			}
			?>
			<div class="avatar"><img class="discuss-avatar" src="<?php echo $avatar;?>" /></div>
			<?php 
			if($view['type']=='ask'){
				echo '<a class="linktaggreen" href="'.site_url('discussion/all?type=ask').'">ask?</a>';
			}else{
				echo '<a class="linktagblue" href="'.site_url('discussion/all?type=thread').'">thread</a>';
			}
			?>					
			<span class="fi-eye"></span> <?php echo $view['views'];?> <span class="fi-comment"></span> <?php echo $this->m_discussion->count_comment($view['id_discuss']);?><br/><small><?php echo '<a target="_blank" href="'.site_url('student/v/'.$view['username']).'">'.$view['username'].'</a> | '.$view['updatedate']?></small>
			<br/>
			<br/>
			<div class="title">
				<h1><a style="font-size:20px" class="linktitle" href="<?php echo site_url('discussion/open/'.$this->uri->segment(3))?>"><?php echo $view['title']?></a> 
					<?php
					$session = $this->session->userdata;
					if(!empty($session['student_login'])){
						if($this->session->userdata['student_login']['id_user']==$view['id_user']){echo '<a title="edit topic" href="'.site_url('discussion/edittopic/'.$this->uri->segment(3)).'"><span class="fi-pencil"></span></a>';}?></h1><br/>
						<?php } ?>
					</div>
					<hr/>
					<p><?php 
						$start = array('[',']');
						$replace = array('<','>');
						$content = str_replace($start, $replace, $view['content']);
						echo $content;
						?></p>
						<br/><br/>
						<div class="col-md-12">
							<?php
							foreach($comments as $c):
								?>
							<div class="comment-item row">
								<div class="small-10 columns">
									<?php
							//encrypt id answer
									$enc_id_answer = base64_encode(base64_encode($c['id_comment']));
									$enc_id_answer = str_replace('=', '', $enc_id_answer);
									if(!empty($c['pp'])){
										$avatar = base_url('assets/img/avatar/'.$c['pp']);
									}else{
										$avatar = base_url('assets/img/avatar.png');
									}
									?>
									<div class="avatar"><img class="discuss-avatar" src="<?php echo $avatar;?>" /></div>
									<small><?php echo '<a href="'.site_url('student/v/'.$c['username']).'">'.$c['username'].'</a>';?></small><br/>
									<small><?php echo $c['commentupdatedate']?></small>
									<br/><br/><br/>
									<p>
										<?php
							//if user logged in is user added answer = update answer
										$session = $this->session->userdata; 
										if(!empty($session['student_login'])){
											if($this->session->userdata['student_login']['username'] == $c['username']){
												echo '<a href="'.site_url('discussion/editanswer/'.$enc_id_answer).'"><span class="fi-pencil"></span> </a>';
											}
										}
										echo $c['comment']?>
									</p>
								</div>
								<div style="float:left" class="small-2 columns">
									<p><a onclick="addLike(<?php echo $c['id_comment']?>)"><span class="fi-arrow-up"></span></a> <span id="like-<?php echo $c['id_comment']?>"><?php echo $this->m_discussion->countCommentAction('up',$c['id_comment']);?></span>
										<br/>
										<a onclick="addDislike(<?php echo $c['id_comment']?>)"><span class="fi-arrow-down"></span></a> <span id="dislike-<?php echo $c['id_comment']?>"><?php echo $this->m_discussion->countCommentAction('down',$c['id_comment']);?></span>
									</p>
								</div>
							</div>
							<br/>
						<?php endforeach;?>
						<!-- post comment -->
						<div class="comment-item row">
							<h4>Post Your Answer</h4>
							<hr/>
							<div class="small-12 columns">
								<?php
								if(!empty($session['student_login']['pp'])){
									$avatar = base_url('assets/img/avatar/'.$this->session->userdata['student_login']['pp']);
								}else{
									$avatar = base_url('assets/img/avatar.png');
								}
								?>
								<div class="avatar"><img class="discuss-avatar" src="<?php echo $avatar;?>" /></div>
								<br/><br/>
								<p>
									<form method="POST" action="">
										<a onclick="showmarkdown()">how to create content</a>
										<div style=" font-size:13px;display:none;background-color:#f4f4f4;padding:5px" id="markdown">
											add command / code : <code>[code]...[/code]</code><br/>
											add link : <code>[url]link[/url]</code><br/>
											add image : <code>[image]image link[/image]</code><br/>
											heading style : <code>[h1]...[/h1] until [h6]...[/h6]</code>
										</div>
										<textarea name="input_comment" style="width:100%;height:150px" placeholder="post here"></textarea>
										<br/>
										<!-- google recaptcha -->
										<div class="g-recaptcha" data-sitekey="6LcaGAQTAAAAAKRuyz9v_cGuKD4i-IzCbPIQgGlQ"></div>
										<!-- end of google recaptcha -->
										<br/>
										<?php
										if(!empty($session['student_login'])){
											echo '<button type="submit" class="button">post</button>';
										}else{
											echo '<a class="button" href="'.site_url('/p/login').'">Login First</a>';
										}
										?>

									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="large-4 columns">
					<?php $this->load->view('discussion/top_discussion')?>
				</div>
			</div>

		</div>

	</div>			

</div>
</div>
<!-- modal -->
<div id="newtopic" class="reveal-modal small" data-reveal>
	<center><h3>What the topic type you want to start</h3></center>
	<hr/>
	<div class="row">
		<div class="large-6 columns">
			<p><center><a style="width:100%" href="<?php echo site_url('discussion/creatediscuss?create=ask')?>" class="button large"><span class=""></span> Ask</a></center>
				Are you stuck? <br/>
				some command does not work <br/>
				ask here
			</p>
		</div>
		<div class="large-6 columns">
			<p><center><a style="width:100%" href="<?php echo site_url('discussion/creatediscuss?create=thread')?>" class="button large"><span class="fi-megaphone"></span> Thread</a></center>
				Do you have an interesting topic to discuss, post here</p>
			</div>
		</div>
		<a class="close-reveal-modal">&#215;</a>
	</div>