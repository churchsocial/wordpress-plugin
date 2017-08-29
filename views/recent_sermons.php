<ul class="church_social_sermon_archive__widget_list">
    <?php if (isset($sermons)): ?>
        <?php if (count($sermons)): ?>
            <?php foreach ($sermons as $sermon): ?>
                <li class="church_social_sermon_archive__widget_item">
                    <div class="church_social_sermon_archive__widget_title">
                        <?php if ($sermon_archive_page_url): ?>
                            <a class="church_social_sermon_archive__widget_link" href="<?php echo $sermon_archive_page_url ?>?sermon_id=<?php echo $sermon['id'] ?>">
                                <?php echo $sermon['title'] ?>
                            </a>
                        <?php else: ?>
                            <?php echo $sermon['title'] ?>
                        <?php endif ?>
                    </div>
                    <div class="church_social_sermon_archive__widget_description">
                        Preached by <?php echo $sermon['author'] ?> on <?php echo date_create($sermon['preached_date'])->format('F j, Y') ?> in the <?php echo strtolower($sermon['preached_time']) ?>.
                    </div>
                </li>
            <?php endforeach ?>
        <?php else: ?>
            <li class="church_social_sermon_archive__widget_item church_social_sermon_archive__widget_no_results">No sermons found.</li>
        <?php endif ?>
    <?php else: ?>
        <li class="church_social_sermon_archive__widget_item church_social_sermon_archive__widget_error_loading">Unable to load sermons.</li>
    <?php endif ?>
</ul>
