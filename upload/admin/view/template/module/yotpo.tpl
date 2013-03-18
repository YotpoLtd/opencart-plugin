<?php echo $header; ?>
<div id="content">

<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>

<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<script type="text/javascript" >
	function sign_up(action) {
		$("#yotpo_form").attr("action", action);
		$('#yotpo_form').submit();
	}
</script>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#yotpo_form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
    <div class="heading">
   	 <h2> <?php echo $heading_settings_title; ?></h2>
  	</div>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="yotpo_form">
      <table class="form">
        <tr>
          <td><?php echo $entry_appkey; ?></td>
          <td><input type="text" name="yotpo_appkey" value="<?php echo $yotpo_appkey; ?>">
              <?php if ($error_appkey) { ?>
                <span class="error"><?php echo $error_appkey; ?></span>
              <?php } ?>
          </td>
        </tr>
        <tr>
          <td><?php echo $entry_secret; ?></td>
          <td><input type="text" name="yotpo_secret" value="<?php echo $yotpo_secret; ?>">
              <?php if ($error_secret) { ?>
                <span class="error"><?php echo $error_secret; ?></span>
              <?php } ?>
          </td>
        </tr>
        <tr>
        <td><?php echo $entry_language; ?></td>
          <td>
            <select name="yotpo_language">
              <?php if($yotpo_language == 'en' ) { ?>
                <option value="en" selected="selected">English</option>                                  
              <?php } else { ?>
                <option value="en">English</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'de' ) { ?>
                <option value="de" selected="selected">German</option>                                   
              <?php } else { ?>
                <option value="de">German</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'es' ) { ?>
                <option value="es" selected="selected">Spanish</option>                                  
              <?php } else { ?>
                <option value="es">Spanish</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'fr' ) { ?>
                <option value="fr" selected="selected">French</option>                                   
              <?php } else { ?>
                <option value="fr">French</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'he' ) { ?>
                <option value="he" selected="selected">Hebrew</option>                                   
              <?php } else { ?>
                <option value="he">Hebrew</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'hr' ) { ?>
                <option value="hr" selected="selected">Croatian</option>                                   
              <?php } else { ?>
                <option value="hr">Croatian</option>
              <?php } ?> 
              
              <?php if($yotpo_language == 'it' ) { ?>
                <option value="it" selected="selected">Italian</option>                                  
              <?php } else { ?>
                <option value="it">Italian</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'ja' ) { ?>
                <option value="ja" selected="selected">Japanese</option>                                   
              <?php } else { ?>
                <option value="ja">Japanese</option>
              <?php } ?>

              <?php if($yotpo_language == 'nl' ) { ?>
                <option value="nl" selected="selected">Dutch</option>                                  
              <?php } else { ?>
                <option value="nl">Dutch</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'pt' ) { ?>
                <option value="pt" selected="selected">Portuguese</option>                                   
              <?php } else { ?>
                <option value="pt">Portuguese</option>
              <?php } ?> 
              
              <?php if($yotpo_language == 'sv' ) { ?>
                <option value="sv" selected="selected">Swedish</option>                                  
              <?php } else { ?>
                <option value="sv">Swedish</option>
              <?php } ?>
              
              <?php if($yotpo_language == 'vi' ) { ?>
                <option value="vi" selected="selected">Vietnamese</option>                                   
              <?php } else { ?>
                <option value="vi">Vietnamese</option>
              <?php } ?>                        
            </select>
          </td>
        </tr>
        <tr>
          <td><?php echo $entry_review_tab_name; ?></td>
          <td><input type="text" name="yotpo_review_tab_name" value="<?php echo $yotpo_review_tab_name; ?>">
          </td>
        </tr>
        <tr>
          <td><?php echo $entry_widget_location; ?></td>
          <td>
            <select name="yotpo_widget_location">            
                <?php if($yotpo_widget_location == 'footer' ) { ?>
                  <option value="footer" selected="selected">Footer</option>                                   
                <?php } else { ?>
                  <option value="footer">Footer</option>
              <?php } ?>
                <?php if($yotpo_widget_location == 'tab' ) { ?>
                  <option value="tab" selected="selected">Tab</option>                                   
                <?php } else { ?>
                  <option value="tab">Tab</option>
              <?php } ?>
                <?php if($yotpo_widget_location == 'other' ) { ?>
                  <option value="other" selected="selected">Other</option>                                   
                <?php } else { ?>
                  <option value="other">Other</option>
              <?php } ?>                                      
            </select>
          </td>
        </tr>  
        <tr>
        	<td><?php echo $entry_bottom_line; ?></td>
        	<td>
	        	<?php if($yotpo_bottom_line_enabled == 'on') { ?>
	        		<input type='checkbox' name='yotpo_bottom_line_enabled' checked='checked' />
	        	<?php } else { ?>
	        		<input type='checkbox' name='yotpo_bottom_line_enabled'/>
	        	<?php } ?>
        	</td>        	
        </tr>               
      </table>
      
	  <div class="heading">
	   <h2> <?php echo $heading_signup_title; ?></h2>
	  </div>      
      <table class="form">
        <tr>
          <td><?php echo $entry_user_name; ?></td>
          <td><input type="text" name="yotpo_user_name" value="<?php echo $yotpo_user_name; ?>">
              <?php if ($error_user_name) { ?>
                <span class="error"><?php echo $error_user_name; ?></span>
              <?php } ?>
          </td>
        </tr> 
        <tr>
          <td><?php echo $entry_email; ?></td>
          <td><input type="text" name="yotpo_email" value="<?php echo $yotpo_email; ?>">
              <?php if ($error_email) { ?>
                <span class="error"><?php echo $error_email; ?></span>
              <?php } ?>
          </td>
        </tr>
        <tr>
          <td><?php echo $entry_password; ?></td>
          <td><input type="password" name="yotpo_password" value="<?php echo $yotpo_password; ?>">
              <?php if ($error_password) { ?>
                <span class="error"><?php echo $error_password; ?></span>
              <?php } ?>
          </td>
        </tr> 
        <tr>
          <td><?php echo $entry_confirm_password; ?></td>
          <td><input type="password" name="yotpo_confirm_password" value="<?php echo $yotpo_confirm_password; ?>">
              <?php if ($error_confirm_password) { ?>
                <span class="error"><?php echo $error_confirm_password; ?></span>
              <?php } ?>
          </td>
        </tr>                                     
      </table>

      <div class="buttons">
      	<a onclick='sign_up("<?php echo $sign_up; ?>");' class="button"><span><?php echo 'Sign up'; ?></span></a>
      	</div>            
    </form>
  </div>
</div>

<?php echo $footer; ?>