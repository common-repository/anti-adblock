<?php extract($aab_options); ?>
<script type="text/javascript">
function confirm_reset() {
  var answer = confirm("All of your custom messages will also be reset.  Are you sure you want to reset all settings?");
  if(answer)
    return true;
  else
    return false;
}
</script>
<form method="post"><fieldset>
<?php
    // check options
    if ( !array_key_exists ( 'show_link', $aab_options )
      || !array_key_exists ( 'can_close', $aab_options )
      || !array_key_exists ( 'visits_till_nag', $aab_options )
      || !array_key_exists ( 'max_nag', $aab_options )
      || !array_key_exists ( 'image', $aab_options )
      || !array_key_exists ( 'message', $aab_options )
      || !array_key_exists ( 'message_css', $aab_options )
      || !array_key_exists ( 'message_bar', $aab_options )
      || !array_key_exists ( 'message_bar_css', $aab_options )
      || !array_key_exists ( 'use_message_bar', $aab_options ) ) {
      printf('
        <div style="background-color:#FFFBCC;border:1px solid #E6DB55;margin:10px 0;padding:5px;">%s&nbsp;&nbsp;<input type="submit" name="aab_options_upgrade_submit" value="%s &#187;"/></div>
        ',
        __('Some option settings are missing (possibly from plugin upgrade).  Please reactivate.', $this->name),
        __('Reactivate', $this->name)
      );
    }
    if ($show_link) {
      $show_link_check1 = 'checked';
      $show_link_check2 = '';
    }
    else {
      $show_link_check1 = '';
      $show_link_check2 = 'checked';
    }

    if ($use_message_bar)
      $use_message_bar_check = 'checked';
    else
      $use_message_bar_check = '';

    if ($can_close)
      $can_close_check = 'checked';
    else
      $can_close_check = '';

    printf('
      <h2>%s</h2>
      <p><label>%s &nbsp; <input name="aab_options_update[show_link]" value="on" type="radio" '.$show_link_check1.'/></label></p>
      <p><label>%s <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=thaya.kareeson@gmail.com&currency_code=USD&amount=&return=&item_name=Donate+a+cup+of+coffee+or+two+to+support+Anti-AdBlock+plugin.">%s</a> %s &nbsp; <input name="aab_options_update[show_link]" value="off" type="radio" '.$show_link_check2.'/></label></p>
      <h2>General Configurations</h2>
      <p><label>%s &nbsp; <input type="text" size="48" value="%s,%s,%s" readonly disabled/></label></p>
      <p><label>%s &nbsp; <input name="aab_options_update[visits_till_nag]" type="text" size="5" value="%s"/></label></p>
      <p><label>%s &nbsp; <input name="aab_options_update[max_nag]" type="text" size="5" value="%s"/></label></p>
      <h2>Message Bar Mode</h2>
      <p><label>%s &nbsp; <input name="aab_options_update[use_message_bar]" type="checkbox" '.$use_message_bar_check.'/> <span title="%s">(%s)</span></label></p>
      <p><label>%s<br/><textarea name="aab_options_update[message_bar]" style="width:600px" rows="3">%s</textarea></label></p>
      <p><label>%s<br/><textarea name="aab_options_update[message_bar_css]" style="width:300px" rows="8">%s</textarea></label></p>
      <h2>Notification Message</h2>
      <p><label>%s &nbsp; <input name="aab_options_update[can_close]" type="checkbox" '.$can_close_check.'/></label></p>
      <p><label>%s<br/><input name="aab_options_update[image]" type="text" size="100" value="%s"/></label></p>
      <p><label>%s<br/><textarea name="aab_options_update[message]" style="width:600px" rows="5">%s</textarea></label></p>
      <p><label>%s<br/><textarea name="aab_options_update[message_css]" style="width:300px" rows="8">%s</textarea></label></p>
      ',
      __('Support this plugin!', $this->name),
      __('Display "Powered by Anti-AdBlock" link at the bottom right corner of the AdBlock notification message', $this->name),
      __('Do not display "Powered by Anti-AdBlock" link.  I will ', $this->name),
      __('donate', $this->name),
      __('and/or write about this plugin', $this->name),
      __('Your current nonces.  Automatically re-generated everytime you visit this page.', $this->name),
      attribute_escape($nonce_message),
      attribute_escape($nonce_message_bar),
      attribute_escape($nonce_banner),
      __('Number of visits until Anti-AdBlock is activated.', $this->name),
      attribute_escape($visits_till_nag),
      __('Maximum number of times that Anti-AdBlock is activated per visitor.', $this->name),
      attribute_escape($max_nag),
      __('Enable message bar mode.', $this->name),
      __('When enabled, Anti-AdBlock plugin will become less intrusive to the reader by showing a popup bar at the top of the browser instead of an AdBlock notification message.  The visitor can then click a link in the bar to view the AdBlock notification message.', $this->name),
      __('What is this?', $this->name),
      __('Message bar text. Use <em>&lt;a id="show-adblock-message" href="#"&gt;show-link&lt;/a&gt;</em> as the link for showing AdBlock notification message.', $this->name),
      attribute_escape($message_bar),
      __('CSS for message bar.', $this->name),
      attribute_escape($message_bar_css),
      __('Allow users to close AdBlock notification message.', $this->name),
      __('Image to display along with the AdBlock notification message.', $this->name),
      attribute_escape($image),
      __('AdBlock notification message to display to visitors with AdBlock enabled.', $this->name),
      attribute_escape($message),
      __('CSS for notification message.', $this->name),
      attribute_escape($message_css)
    );

    if ( function_exists( 'wp_nonce_field' ) && wp_nonce_field( $this->name ) ) {
      printf('
        <p class="submit">
          <input type="submit" name="aab_options_update_submit" value="%s &#187;" />
          <input type="submit" name="aab_options_reset_submit" value="Reset ALL Options &#187;" onclick="return confirm_reset()"/>
        </p>
        ',
        __('Update Options', $this->name),
        __('Reset ALL Options', $this->name)
      );
    }
?>
</fieldset></form>
