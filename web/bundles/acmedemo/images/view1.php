<?php if($_GET['id']=='1'){?>
<?php $fet=$functions->get_video();  ?>
<div class="row-10 bg-gray1">
   
    <div class="padding-10"><?=strtoupper($fet->unit_name)?></div>
    
    
    <div class="video"> 
      
      
 <!-- Video------------>
  <link rel="stylesheet" href="video/themes/maccaco/projekktor.style.css" type="text/css" media="screen" />

    <script type="text/javascript" src="video/jquery-1.9.1.min.js"></script>
 <!-- load projekktor -->
    <script type="text/javascript" src="video/projekktor-1.3.09.min.js"></script>
	 <div id="player_a" class="projekktor"></div>

    <script type="text/javascript">
    $(document).ready(function() {
        projekktor('#player_a', {
        poster: 'media/intro.png',
        title: 'this is projekktor',
        playerFlashMP4: 'video/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
        playerFlashMP3: 'video/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
        width: 683,
        height: 400,
        playlist: [
            {
            1: {src: "awn-admin/assets/images/<?=$fet->filename?>", type: "video/mp4"}

            }
        ]    
        }, function(player) {} // on ready 
        );
    });
    </script>

      <!--Employee Engagement-Unit 1.mp4-->
      
    </div>
    
    <div class="padding-10 bg-gray1">
    
    <a href="#" class="gray-btn float-left"><img src="assets/images/icons/pre.png" /> <span class="color-white">PREVIOUS</span></a>
    
    <a href="#" class="gray-btn float-right"><span class="color-white"> NEXT </span> <img src="assets/images/icons/next.png" /></a>
    
    <div class="clear"></div>
    </div>
   
   </div>
<?php  } ?>