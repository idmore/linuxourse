<center>
	<div class="large-10 large-offset-1 columns">
		<div class="row">
			<br/>
			<form method="GET" action="<?php echo site_url('discussion/search/');?>">
				<div class="large-12 columns">
					<div class="row collapse">
						<div class="small-11 columns">
							<input name="q" value="<?php if(!empty($recentq))echo $recentq;?>" style="height:40px" type="text" placeholder="Search Topik">
						</div>
						<div class="small-1 columns">
							<button type="submit" style="height:40px" href="#" class="button postfix"><h3 style="color:#fff"><span class="fi-magnifying-glass"></span></h3></button>
						</div>
					</div>
				</form>
			</div>
			<p> or <a data-reveal-id="newtopic" href="#">create new topic</a></p>
		</form>
		<br/>
	</div>
</div>
</center>
<div class="row">
	<div class="large-12 columns">
		<div class="large-12 columns">
			<strong>order by : </strong><a id="orderAll" href="<?php echo site_url('discussion/all')?>">Lattest</a> | <a id="orderViews" href="<?php echo site_url('discussion/orderby/views')?>">Top Views</a> | <a id="orderComments" href="#">Top Comment</a>
			<?php
		//if login show my topics n my comments
			if(!empty($this->session->userdata['student_login']))
				echo '| <a id="orderTopics" href="'.site_url('discussion/mytopics').'">My Topics</a> | <a id="orderAnswers" href="'.site_url('discussion/myanswers').'">My Answers</a>';
			?>	
			<hr/>
		</div>