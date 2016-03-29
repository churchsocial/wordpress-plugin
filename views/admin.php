<div class="church_social">
    <div class="wrap">
        <h2 class="church_social__logo">
            <a href="http://churchsocialapp.com">
                <img src="<?php echo plugins_url('img/logo.png', dirname(__FILE__)) ?>" height="60">
            </a>
        </h2>
        <p class="description">
            This plugin allows churches to display content from their <a href="http://churchsocialapp.com">Church Social</a> account on their WordPress website.
        </p>
        <form method="post" action="options.php">
            <?php settings_fields('church_social') ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API key:</th>
                    <td>
                        <input type="text" name="church_social_api_key" value="<?php echo get_option('church_social_api_key') ?>" style="min-width: 300px;" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Calendar page:</th>
                    <td>
                        <select name="church_social_calendar_page_id" style="min-width: 300px;">
                            <option></option>
                            <?php foreach (get_pages(['post_status' => 'publish,inherit,pending,private,future,draft,trash']) as $page): ?>
                                <?php if (get_option('church_social_calendar_page_id') === (string) $page->ID): ?>
                                    <option selected="selected" value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Sermon archive page:</th>
                    <td>
                        <select name="church_social_sermon_archive_page_id" style="min-width: 300px;">
                            <option></option>
                            <?php foreach (get_pages(['post_status' => 'publish,inherit,pending,private,future,draft,trash']) as $page): ?>
                                <?php if (get_option('church_social_sermon_archive_page_id') === (string) $page->ID): ?>
                                    <option selected="selected" value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Theme:</th>
                    <td>
                        <select name="church_social_theme" style="min-width: 300px;">
                            <option></option>
                            <?php foreach (['light', 'dark'] as $theme): ?>
                                <?php if (get_option('church_social_theme') === $theme): ?>
                                    <option value="<?php echo $theme ?>" selected="selected"><?php echo ucwords($theme) ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $theme ?>"><?php echo ucwords($theme) ?></option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                        <p class="description">Helpful on non Church Social themes.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes" />
            </p>
        </form>
    </div>
</div>