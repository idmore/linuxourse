d<script type="text/javascript">
$(document).ready(function(){
  getDirectory();    
});

function addDir(){
  $('#addDir').toggle('fast');
  $('#addFile').hide('fast');
}
function addFile(){
  $('#addFile').toggle('fast');
  $('#addDir').hide('fast');
}
  //add new dir
  function procAddDir(){
    pwd = $('#location').val();
    newDir = $('#newDir').val();
    url = '<?php echo site_url("manage/crudStorage?act=adddir")?>';
    if(pwd == '/'){
      dir = '/'+newDir;
    }else{
      dir = pwd+'/'+newDir;
    }
    //insert to database
    $.ajax({
      url:url,
      data:{name:dir},
      success:function(){
        alert('Success Add New Directory');
        $('#newDir').val('');
        getDirectory();//show lattest directory
      },  
      error:function(){
        alert('Error Add Directory, Directory is available');
      }
    });
  }
  //add new file
  function procAddFile(){
    pwd = $('#location').val();
    //get file name
    type = $('#filetype').val();
    name = $('#filename').val();
    attributes = $('#fileattributes').val();
    content = $('#filecontent').val();
    //ajax process
    url='<?php echo site_url("manage/crudStorage?act=addfile")?>';
    $.ajax({
      url:url,
      data:{dir:pwd,type:type,name:name,attributes:attributes,content:content},
      success:function(){
        alert('Success add File');
        $('#addFile').hide('fast');
        getDirectory();//show lattest directory
      },
      error:function(){
        alert('Error Add File, File is available')
      }
    });
  }
  //delete file
  function deleteFile(id){
    confirmation = confirm('are you sure');
    url='<?php echo site_url("manage/crudStorage?act=deletefile")?>';
    if(confirmation==1){
      $.ajax({
        url:url,
        data:{id:id},
        success:function(){
          alert('Delete file success');
          getDirectory();
        },
        error:function(){
          alert('Error delete file');
        }
      });
    }
  }
  //change directory
  function changeDirectory(dir){
    $('#location').val(dir);
    getDirectory(dir);
  }
  //edit file : show
  function editFileView(id){
    // alert('betul');
    $.ajax({
      url:'<?php echo site_url("manage/crudStorage?act=vieweditfile")?>',
      data:{id:id},
      success:function(response){
        $('#editFile').html(response);
        $('#editFile').show('fast');
      },
      error:function(){
        alert('Something wrong, please refresh page');
      }
    });
  }
  //update file on db
  function procEditFile(){

  }
  //back directory
  function backDirectory(){
    pwd = $('#location').val();
    if(pwd == '/'){
      alert('your location is ROOT');
    }else{
      alert('back to parent');
    }
  }
  //get directory
  function getDirectory(){
    $('#listcontent').html('<center>loading content...</center>');
    directory = $('#location').val();
    url= '<?php echo site_url("manage/getdirectory")?>';
    $.ajax({
      url:url,
      data:{dir:directory},
      success:function(response){
        $('#listcontent').html(response);
      },
      error:function(){
        alert('something wrong');
      }
    });
  }
  //delete directory
  function deleteDirectory(dir){
    confirmation = confirm('Are You Sure');
    // alert(dir);
    if(confirmation == 1){
      ///delete from db
      url='<?php echo site_url("manage/crudstorage?act=deletedir")?>';
      $.ajax({
        url:url,
        data:{dir:dir},
        success:function(response){
          alert('Success delete directory');
          getDirectory();//show lattest directory
        },
        error:function(){
          alert('Error delete direktori/direktori not available');
        }
      });
    }
  }
  //edit directory :: view
  function editDirectory(dir){
    $('#editNewDir').val(dir);
    $('#recentNewDir').val(dir);
    $('#editDir').show('fast');
  }
  //edit file :: view
  function editFile(dir){
    $('#editNewFile').val(dir);
    $('#recentNewFile').val(dir);
    $('#editFile').show('fast');
  }
  //process edit file
  function processEditFile(){
    id = $('#editfileid').val();
    type = $('#editfiletype').val();
    name = $('#editfilename').val();
    attributes = $('#editfileattributes').val();
    content = $('#editfilecontent').val();
    //process edit file    
    $.ajax({
      url:'<?php echo site_url("manage/crudstorage?act=proceditfile");?>',
      data:{id:id,type:type,name:name,attributes:attributes,content:content},
      success:function(){
        alert('Success edit file');
        $('#editFile').hide('fast');
        getDirectory();
      },
      error:function(){
        alert('Error update file');
      }
    });
  }
  //process edit directory
  function procEditDir(){
    newdir = $('#editNewDir').val();//new dir name
    olddir = $('#recentNewDir').val();//old dir name
    url = '<?php echo site_url("manage/crudstorage?act=editdir")?>';
    //update database
    $.ajax({
      url:url,
      data:{newdir:newdir,olddir:olddir},
      success:function(response){
        // alert(response);
        alert('Success update directory');
        $('#editDir').hide('fast');
        getDirectory();//show lattest directory
      },
      error:function(){
       alert('Error update direktori/direktori not available');
     }
   });
  }
</script>
<!--body-->
<section  id="welcome">
  <!--login form-->
  <div class="login-form-block">
    <!--empty div-->
    <div class="row admin-content">
      <!-- sidebar -->
      <div class="large-2 columns">
       <?php $this->load->view('manage/sidebar');?>
     </div>
     <!-- end of sidebar -->
     <!-- content -->
     <div class="large-10 columns">
      <nav class="breadcrumbs" role="menubar" aria-label="breadcrumbs">
        <li role="menuitem"><a href="#">Manage</a></li>
        <li role="menuitem" class="current"><a href="#">Storage</a></li>
      </nav>
      <br/>
      <?php if(!empty($_GET['success'])){//if add note ?>
      <div data-alert class="alert-box success">
        <?php echo $_GET['success'];?>
        <a href="#" class="close">&times;</a>
      </div>
      <?php }else if(!empty($_GET['error'])){?>
      <div data-alert class="alert-box error">
        <?php echo $_GET['error'];?>
        <a href="#" class="close">&times;</a>
      </div>
      <?php }?>
      <br/>
      <dl class="sub-nav">
        <dt>Filter:</dt>
        <dd class="active"><a href="#">All</a></dd>
      </dl>
      <div class="admin-content-white">
        <span class="row collapse" style="min-width:100%">
          <span class="large-10 columns">
            <input id="location" type="text" placeholder="location" value="/"></span>
            <span class="large-2 columns"><button  onclick="getDirectory()" class="button tiny">jump to location</button>
            </span>
          </span>
          <!-- data from db -->
          <?php
          $uri = $this->uri->segment(3)+1;
          if(!$uri){
            $uri = 1;
          }
          ?>
          <a style="width:100px;" onclick="addDir()"><span style="font-size:15px;padding:5px;background-color:#008cba;color:#fff" class="fi-folder-add"> Add Directory</span></a>
          <a style="width:100px;" onclick="addFile()"><span style="font-size:15px;padding:5px;background-color:#008cba;color:#fff" class="fi-page-add"> Add File</span></a>
          <hr/>
          <div id="editDir" class="form-add">
            <label>Directory Name <a onclick="return  $('#editDir').hide('fast');">[X]</a> 
              <input type="text" id="editNewDir"></label>
              <input type="hidden" id="recentNewDir">
              <br/>
              <a onclick="procEditDir()" class="button small">save changes</a>
            </div>
            <div id="editFile" class="form-add"></div>
            <div id="addDir" class="form-add">
             <label>Directory Name<input type="text" id="newDir"></label>
             <br/>
             <a onclick="procAddDir()" class="button small">add</a>
           </div>
           <div id="addFile" class="form-add">
            <label>File Name<input id="filename" type="text"></label>
            <label>Type
              <select id="filetype">
                <option value="-">file</option>
                <option value="s">softlink</option>
              </select>
            </label>
            <br/>
            <label>Attributes
              <input type="text" id="fileattributes" value="rwx--x--x:0 user user 7000 1Jan2015 24:00">
            </label>
            <label>Content
              <textarea id="filecontent"></textarea>
            </label>
            <br/>
            <button onclick="procAddFile()" class="button small">add file</button>
          </div>
          <div class="row">
            <div class="large-12 columns">
              <a onclick="changeDirectory('/')">/</a>
            </div>
            <div class="large-12 columns">
              <a onclick="backDirectory()">..</a>
            </div>
          </div>
          <div id="listcontent"></div>
          <!-- data from db -->
        </div>
      </div>
      <!-- end of content -->
    </div>
  </div>
  <!--end login form -->
</section>
<!--endof body-->