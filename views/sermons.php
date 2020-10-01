<div class="church_social_sermon_archive">

    <?php if ($this->authors): ?>
        <form class="church_social_sermon_archive__search_form">
            <input type="hidden" name="sermon_page" value="1">
            <p>
                <label class="church_social_sermon_archive__search_form_minister_label" for="church_social_sermon_archive__search_form_minister_select">Minister:</label>
                <select class="church_social_sermon_archive__search_form_minister_select" name="author_id" id="church_social_sermon_archive__search_form_minister_select">
                    <option></option>
                    <?php foreach ($this->authors as $author): ?>
                        <?php if (isset($_GET['author_id']) and $_GET['author_id'] == $author['id']): ?>
                            <option value="<?php echo $author['id'] ?>" selected="selected">
                                <?php echo $author['last_name'] ?>, <?php echo $author['title'] ? $author['title'].' '.$author['first_name'] : $author['first_name'] ?>
                            </option>
                        <?php else: ?>
                            <option value="<?php echo $author['id'] ?>">
                                <?php echo $author['last_name'] ?>, <?php echo $author['title'] ? $author['title'].' '.$author['first_name'] : $author['first_name'] ?>
                            </option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
                <button class="church_social_sermon_archive__search_form_button" type="submit">Search</button>
            </p>
        </form>
    <?php endif ?>

    <table class="church_social_sermon_archive__table" width="100%">
        <thead>
            <tr>
                <th class="church_social_sermon_archive__table_date_header" align="left" width="10%">Date</th>
                <th class="church_social_sermon_archive__table_title_header" align="left" width="40%">Title</th>
                <th class="church_social_sermon_archive__table_author_header" align="left" width="20%">Minister</th>
                <th class="church_social_sermon_archive__table_passage_header" align="left" width="20%">Key Text</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($this->sermons): ?>
                <?php foreach ($this->sermons as $sermon): ?>
                    <tr>
                        <td class="church_social_sermon_archive__table_date">
                            <?php if ($sermon['time']): ?>
                                <?php echo ChurchSocial\Util::date($sermon['date'], 'M j, Y') ?> <span class="church_social_sermon_archive__table_time">(<?php echo $sermon['time'] ?>)</span>
                            <?php else: ?>
                                <?php echo ChurchSocial\Util::date($sermon['date'], 'M j, Y') ?>
                            <?php endif ?>
                        </td>
                        <td class="church_social_sermon_archive__table_title"><a href="?sermon_id=<?php echo $sermon['id'] ?>"><?php echo $sermon['title'] ?></a></td>
                        <td class="church_social_sermon_archive__table_author">
                            <?php if ($sermon['author'] && !$sermon['read_by_name']): ?>
                                <?php echo $sermon['author']['name'] ?>
                            <?php elseif (!$sermon['author'] && $sermon['read_by_name']): ?>
                                Read by <?php echo $sermon['read_by_name'] ?>
                            <?php elseif ($sermon['author'] && $sermon['read_by_name']): ?>
                                <div><?php echo $sermon['author']['name'] ?></div>
                                <div>(Read by <?php echo $sermon['read_by_name'] ?>)</div>
                            <?php endif ?>
                        </td>
                        <td class="church_social_sermon_archive__table_passage">
                            <?php if ($sermon['key_text']): ?>
                                <?php echo $sermon['key_text'] ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td class="church_social_sermon_archive__table_no_sermons_found" colspan="4">No sermons found.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>

    <p class="church_social_sermon_archive__prev_and_next_page_buttons">
        <?php if ($this->meta['previous_page']): ?>
            <a class="church_social_sermon_archive__prev_page_button" href="?sermon_page=<?php echo $this->meta['previous_page']?><?php echo isset($_GET['author_id']) ? '&author_id='.$_GET['author_id'] : '' ?>">
                Previous page
            </a>
        <?php endif ?>
        <?php if ($this->meta['next_page']): ?>
            <a class="church_social_sermon_archive__next_page_button" href="?sermon_page=<?php echo $this->meta['next_page']?><?php echo isset($_GET['author_id']) ? '&author_id='.$_GET['author_id'] : '' ?>">
                Next page
            </a>
        <?php endif ?>
    </p>

</div>
