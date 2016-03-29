<li class="widget">
    <h2>Recent Sermons</h2>
    <ul>
        <?php if (isset($sermons)): ?>
            <?php foreach ($sermons as $sermon): ?>
                <li>
                    <div class="church_social_sermon_archive__widget_title">
                        <?php if ($sermon_archive_page_url): ?>
                            <a href="<?php echo $sermon_archive_page_url ?>?sermon_id=<?php echo $sermon['id'] ?>">
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
            <li>Unable to load sermon archive data.</li>
        <?php endif ?>
    </ul>
</li>