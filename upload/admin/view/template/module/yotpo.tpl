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

<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
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
              <option value="en" {if $yotpo_language == "en"}selected{/if}>English</option>
              <option value="de" {if $yotpo_language == "de"}selected{/if}>German</option>
              <option value="es" {if $yotpo_language == "es"}selected{/if}>Spanish</option>
              <option value="fr" {if $yotpo_language == "fr"}selected{/if}>French</option>
              <option value="he" {if $yotpo_language == "he"}selected{/if}>Hebrew</option>
              <option value="hr" {if $yotpo_language == "hr"}selected{/if}>Croatian</option>
              <option value="it" {if $yotpo_language == "it"}selected{/if}>Italian</option>
              <option value="ja" {if $yotpo_language == "ja"}selected{/if}>Japanese</option>
              <option value="nl" {if $yotpo_language == "nl"}selected{/if}>Dutch</option>
              <option value="pt" {if $yotpo_language == "pt"}selected{/if}>Portuguese</option>
              <option value="sv" {if $yotpo_language == "sv"}selected{/if}>Swedish</option>
              <option value="vi" {if $yotpo_language == "vi"}selected{/if}>Vietnamese</option>
            </select>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>

<?php echo $footer; ?>