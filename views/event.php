<div class="church_social_calendar">
    <h2 class="church_social_calendar__event_title">
        <?php echo $this->event['title'] ?>
    </h2>

    <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--date">Date:</h3>
    <p class="church_social_calendar__event_details church_social_calendar__event_details--date">
        <?php echo $this->event['date']->format('l, F j, Y') ?>
    </p>

    <?php if ($this->event['start_time']): ?>
        <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--date">Time:</h3>
        <p class="church_social_calendar__event_details church_social_calendar__event_details--date">
            <?php echo $this->event['start_time']->format('g:ia') ?>
            <?php if ($this->event['end_time']): ?>
                to <?php echo $this->event['end_time']->format('g:ia') ?>
            <?php endif ?>
        </p>
    <?php endif ?>

    <?php if ($this->event['location']): ?>
        <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--location">Location:</h3>
        <p class="church_social_calendar__event_details church_social_calendar__event_details--location">
            <?php echo $this->event['location'] ?>
        </p>
    <?php endif ?>

    <?php if ($this->event['description']): ?>
        <h3 class="church_social_calendar__event_sub_title church_social_calendar__event_sub_title--description">Description:</h3>
        <p class="church_social_calendar__event_details church_social_calendar__event_details--description">
            <?php echo nl2br($this->event['description']) ?>
        </p>
    <?php endif ?>

    <p class="church_social_calendar__event_buttons">
        <a class="church_social_calendar__event_back_button" href="?month=<?php echo $this->event['date']->format('Y-m') ?>">Back to calendar</a>
    </p>
</div>
