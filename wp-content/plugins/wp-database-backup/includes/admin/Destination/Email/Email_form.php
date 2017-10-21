<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseII">
                                <h2>Email Notification</h2>

                            </a>
                        </h4>
                    </div>
                    <div id="collapseII" class="panel-collapse collapse in">
                        <div class="panel-body">

                            <?php
                            echo '<form name="wp-email_form" method="post" action="" >';
                            wp_nonce_field('wp-database-backup'); 

                            $wp_db_backup_email_id = "";
                            $wp_db_backup_email_id = get_option('wp_db_backup_email_id');
                            $wp_db_backup_email_attachment = "";
                            $wp_db_backup_email_attachment = get_option('wp_db_backup_email_attachment');
                            $wp_db_backup_destination_Email=get_option('wp_db_backup_destination_Email');
                            echo '<p>';
                            echo '<span class="glyphicon glyphicon-envelope"></span> Send Email Notification</br></p>';
                            $ischecked=(isset($wp_db_backup_destination_Email) && $wp_db_backup_destination_Email==1) ? 'checked' : '';
                            echo '<div class="row form-group">
                                <label class="col-sm-2" for="wp_db_backup_destination_Email">Enable Email Notification:</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" '.$ischecked.' id="wp_db_backup_destination_Email" name="wp_db_backup_destination_Email">
                                </div>
                            </div>';
                            echo '<div class="row form-group"><label class="col-sm-2" for="wp_db_backup_email_id">Email Id</label>';
                            echo '<div class="col-sm-6"><input type="text" id="wp_db_backup_email_id" class="form-control" name="wp_db_backup_email_id" value="' . $wp_db_backup_email_id . '" placeholder="Your Email Id"></div>';
                            echo '<div class="col-sm-4">Leave blank if you don\'t want use this feature or Disable Email Notification</div></div>';
                            echo '<div class="row form-group"><label class="col-sm-2" for="lead-theme">Attach backup file </label> ';
                            $selected_option = get_option('wp_db_backup_email_attachment');

                            if ($selected_option == "yes")
                                $selected_yes = "selected=\"selected\"";
                            else
                                $selected_yes = "";
                            if ($selected_option == "no")
                                $selected_no = "selected=\"selected\"";
                            else
                                $selected_no = "";
                            echo '<div class="col-sm-2"><select id="lead-theme" class="form-control" name="wp_db_backup_email_attachment">';
                            echo '<option value="none">Select</option>';

                            echo '<option  value="yes"' . $selected_yes . '>Yes</option>';
                            echo '<option  value="no" ' . $selected_no . '>No</option>';


                            echo '</select></div>';

                            echo '<div class="col-sm-8">If you want attache backup file to email then select "yes" (File attached only when backup file size <=25MB)</div>';

                            echo '</div>';
                            echo '<p class="submit">';
                            echo '<input type="submit" name="Submit" class="btn btn-primary" value="Save Settings" />';
                            echo '</p>';
                            echo '</form>';
                            ?>
                        </div>		
                    </div>
                </div>
                