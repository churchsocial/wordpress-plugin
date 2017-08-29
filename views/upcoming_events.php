<ul class="church_social_calendar__widget_list">
    <?php if (isset($events)): ?>
        <?php if (count($events)): ?>
            <?php foreach ($events as $event): ?>
                <li class="church_social_calendar__widget_item">
                    <div class="church_social_calendar__widget_title">
                        <?php if ($calendar_page_url): ?>
                            <a class="church_social_calendar__widget_link" href="<?php echo $calendar_page_url ?>?event_id=<?php echo $event['id'] ?>&amp;event_date=<?php echo date_create($event['date'])->format('Y-m-d') ?>">
                                <?php echo $event['title'] ?>
                            </a>
                        <?php else: ?>
                            <?php echo $event['title'] ?>
                        <?php endif ?>
                    </div>
                    <div class="church_social_calendar__widget_description">
                        <?php if (date_create($event['date'])->format('H:i:s') === '00:00:00'): ?>
                            <?php echo date_create($event['date'])->format('l, F j, Y') ?>
                        <?php else: ?>
                            <?php echo date_create($event['date'])->format('l, F j, Y \a\t g:i A') ?>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach ?>
        <?php else: ?>
            <li class="church_social_calendar__widget_item church_social_calendar__widget_no_results">No events found.</li>
        <?php endif ?>
    <?php else: ?>
        <li class="church_social_calendar__widget_item church_social_calendar__widget_error_loading">Unable to load events.</li>
    <?php endif ?>
</ul>
