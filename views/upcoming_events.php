<li class="widget">
    <h2>Upcoming Events</h2>
    <ul>
        <?php if (isset($events)): ?>
            <?php foreach ($events as $event): ?>
                <li>
                    <?php if ($calendar_page_url): ?>
                        <a href="<?php echo $calendar_page_url ?>?event_id=<?php echo $event['id'] ?>&amp;event_date=<?php echo date_create($event['date'])->format('Y-m-d') ?>">
                            <?php echo $event['title'] ?>
                        </a>
                    <?php else: ?>
                        <?php echo $event['title'] ?>
                    <?php endif ?>
                    <div>
                        <?php if (date_create($event['date'])->format('H:i:s') === '00:00:00'): ?>
                            <?php echo date_create($event['date'])->format('l, F j, Y') ?>
                        <?php else: ?>
                            <?php echo date_create($event['date'])->format('l, F j, Y \a\t g:i A') ?>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach ?>
        <?php else: ?>
            <li>Unable to load calendar data.</li>
        <?php endif ?>
    </ul>
</li>