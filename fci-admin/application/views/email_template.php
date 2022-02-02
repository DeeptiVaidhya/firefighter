<?php
$site_name = $this->config->item('site_name');
$border_box = "box-sizing: border-box; font-size: 14px; margin: 0;";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      style="box-sizing: border-box; font-size: 14px; margin: 0;">
    <head>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title><?php echo $site_name;?></title>
		<style>.username-heading h2{color:#f47525}</style>
    </head>

    <body style="<?php echo $border_box ?> -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #fff;" bgcolor="#fff">
        <table class="body-wrap" style="<?php echo $border_box ?> width: 100%; background-color: #fff;" bgcolor="#fff;">
            <tr style="<?php echo $border_box ?>">
                <td style=" <?php echo $border_box ?> vertical-align: top;" valign="top"></td>
                <td class="container" style="<?php echo $border_box ?> vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                    <div class="content"
                         style="<?php echo $border_box ?> max-width: 768px; display: block; margin: 0 auto; padding: 20px 0;">
                        <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope
                               itemtype="http://schema.org/ConfirmAction" style="<?php echo $border_box ?> border-radius: 3px; border: none;">
                            <tr style="<?php echo $border_box ?>">
                                <td class="content-wrap" style="<?php echo $border_box ?> vertical-align: top;padding: 30px 0 10px;border-radius: 7px; background-color: #fff;" valign="top">
                                    <meta itemprop="name" content="Confirm Email"
                                          style="<?php echo $border_box ?>"/>
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="<?php echo $border_box ?> text-align: center;">
                                        <tr style="<?php echo $border_box ?>">
                                            <?php $image = 'images/logo.png';?>
                                            <td class="content-block"
                                                style="<?php echo $border_box ?> vertical-align: top; padding-top: 0;padding-bottom: 40px;"
                                                valign="top"><img src="<?php echo assets_url($image); ?>" alt="<?php echo $site_name;?>" style="width: 250px;">
                                            </td>
                                        </tr>
                                        <?php if (isset($heading) && $heading != '') {?>
                                            <tr style="<?php echo $border_box ?>">
                                                <td class="content-block"
                                                    style="font-family: 'Montserrat-Light'; box-sizing: border-box; font-size: 30px; vertical-align: top; margin: 0; padding: 0 0 20px; line-height: 35px; color: #f47525;"
                                                    valign="top"><?php echo $heading; ?></td>
                                            </tr>
                                        <?php }?>
                                        <tr style="<?php echo $border_box ?>">
                                            <?php if (isset($message) && $message != '') {?>
                     							<td class="content-block username-heading" style="box-sizing: border-box; font-size: 18px; font-weight: 600; margin: 0;  padding-left: 10px; padding-right: 10px; padding-bottom: 20px; line-height: 26px;"
                                                    valign="top"><?php echo $message; ?></td>
                                                <?php }?>
                                                <?php if (isset($message_content) && $message_content != '') {?>
                                                <td class="content-block" style="box-sizing: border-box; font-size: 18px; font-weight: 200; margin: 0;  padding-left: 10px; padding-right: 10px; padding-bottom: 20px; line-height: 26px; text-align: left"
                                                    valign="top"><?php echo $message_content; ?></td>
                                                <?php }?>
                                        </tr>
                                        <?php if (isset($btntitle) && $btntitle != '') {?>
                                            <tr style="<?php echo $border_box ?>">
                                                <td class="content-block" style="box-sizing: border-box; font-size: 16px; vertical-align: top; margin: 0; padding-bottom: 40px;padding-left: 10px; padding-right: 10px;"
                                                    valign="top">
                                                    <a href="<?php echo $link; ?>" itemprop="url"
                                                       style=" box-sizing: border-box; font-size: 20px; color: #FFF; text-decoration: none; line-height: 2em; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; background-color: #00502f; margin: 0; border-color: #00502f; border-style: solid; border-width: 6px 16px;width: 280px; margin-top: 20px;"><?php echo $btntitle; ?></a>
                                                </td>
                                            </tr>
                                        <?php }?>
                                        <?php if (isset($link) && $link != '') {?>
                                            <tr style="<?php echo $border_box ?>">
                                                <td class="content-block"
                                                    style=" box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 60px;"
													valign="top">
													<p style="font-size: 16px; color:#4f5359;padding-bottom: 15px;">Or visit this link:</p>
													<p style="font-size: 16px; font-weight: 600;padding-bottom: 15px;"><a style="color: #000000;" href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
													<p style="font-size: 16px; color:#4f5359; padding-bottom: 15px;">If clicking the above link doesn't work, you can copy and paste it into your browser's address window, or retype it there.</p>
                                                </td>
                                            </tr>
										<?php }?>
										<?php if (isset($note) && $note != '') {?>
                                            <tr style="box-sizing: border-box; font-size: 16px; margin: 0; line-height: 26px;">
                                                <td class="content-block" valign="top"
                                                    style="box-sizing: border-box; vertical-align: top; margin: 0; padding: 0 0 60px;">
                                                    <p style="color:#4f5359; padding-bottom: 15px;"><?php echo $note; ?></p>
                                                </td>
                                            </tr>
                                        <?php }?>
                                        <?php if (!isset($is_admin)) {?>
                                            <tr style="<?php echo $border_box ?>background-color: #f7f9fa; margin-top: 30px;">
                                                <td class="content-block" style="box-sizing: border-box; font-size: 16px; vertical-align: middle; margin: 0; padding-top: 40px 20px;"
                                                    valign="top">
                                                    <?php $contact_email = $this->config->item('site_contact_us_email');?>
                                                        <p style="padding-bottom: 20px;"><strong>If you have any questions about this request, please contact <?php echo $site_name;?> Support at <a style="color: #000000;"  href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></strong></p>
                                                    <p>Please do not reply to this email. This mailbox is not monitored and your response will not be processed.</p>
                                                </td>
                                            </tr>
                                        <?php }?>
                                        <tr style="<?php echo $border_box ?>">
                                            <td class="content-block" style="box-sizing: border-box; font-size: 16px; vertical-align: middle; margin: 0; padding: 20px 10px 0;" valign="top">
                                                <p style="color:#4f5359;">&copy;<?php echo $site_name;?> 2020</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="<?php echo $border_box ?>" valign="top"></td>
            </tr>
        </table>
    </body>
</html>
