<div class="church_social_sermon_archive">

    <?php if ($this->authors): ?>
        <form class="church_social_sermon_archive__search_form">
            <p>
                <label class="church_social_sermon_archive__search_form_minister_label" for="church_social_sermon_archive__search_form_minister_select">Minister:</label>
                <select class="church_social_sermon_archive__search_form_minister_select" name="author" id="church_social_sermon_archive__search_form_minister_select">
                    <option></option>
                    <?php foreach ($this->authors as $author): ?>
                        <?php if (isset($_GET['author']) and $_GET['author'] === $author): ?>
                            <option selected="selected"><?php echo $author ?></option>
                        <?php else: ?>
                            <option><?php echo $author ?></option>
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
                <th class="church_social_sermon_archive__table_preached_time_header" align="left" width="10%">Service</th>
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
                            <?php echo date_create($sermon['preached_date'])->format('M j, Y') ?>
                        </td>
                        <td class="church_social_sermon_archive__table_preached_time">
                            <?php if ($sermon['preached_time']): ?>
                                <?php echo $sermon['preached_time'] ?>
                            <?php endif ?>
                        </td>
                        <td class="church_social_sermon_archive__table_title"><a href="?sermon_id=<?php echo $sermon['id'] ?>"><?php echo $sermon['title'] ?></a></td>
                        <td class="church_social_sermon_archive__table_author"><?php echo $sermon['author'] ?></td>
                        <td class="church_social_sermon_archive__table_passage"><?php echo $sermon['passage'] ?></td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td class="church_social_sermon_archive__table_no_sermons_found" colspan="5">No sermons found.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>

</div>
