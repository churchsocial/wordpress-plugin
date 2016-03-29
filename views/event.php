<div class="church_social_calendar">
    <h2 class="church_social_calendar__event_title">
        <?php echo $this->event['title'] ?>
    </h2>

    <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--date">Date:</h3>
    <p class="church_social_calendar__event_details church_social_calendar__event_details--date">
        <?php if ($this->event['date']->format('H:i:s') === '00:00:00'): ?>
            <?php echo $this->event['date']->format('l, F j, Y') ?>
        <?php else: ?>
            <?php echo $this->event['date']->format('l, F j, Y \a\t g:i A') ?>
        <?php endif ?>
    </p>

    <?php if ($this->event['location']): ?>
        <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--location">Location:</h3>
        <p class="church_social_calendar__event_details church_social_calendar__event_details--location">
            <?php echo $this->event['location'] ?>
        </p>
    <?php endif ?>

    <?php if ($this->event['description']): ?>
        <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--description">Description:</h3>
        <p class="church_social_calendar__event_details church_social_calendar__event_details--description">
            <?php echo $this->event['description'] ?>
        </p>
    <?php endif ?>

    <p class="church_social_calendar__event_buttons">
        <a class="church_social_calendar__event_back_button" href="?month=<?php echo $this->event['date']->format('Y-m') ?>">Back to calendar</a>
    </p>
</div>