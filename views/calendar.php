<div class="church_social_calendar">
    <h2 class="church_social_calendar__title"><?php echo $this->current_month->format('F Y') ?></h2>
    <div class="church_social_calendar__controls">
        <a class="church_social_calendar__controls_link" href="?month=<?php echo $this->previous_month->format('Y-m') ?>">Previous Month</a>
        <a class="church_social_calendar__controls_link" href="?month=<?php echo date_create()->format('Y-m') ?>">Today</a>
        <a class="church_social_calendar__controls_link" href="?month=<?php echo $this->next_month->format('Y-m') ?>">Next Month</a>
    </div>
    <table class="church_social_calendar__table">
        <tbody>
            <tr>
                <?php
                    $running_day = (int) $this->current_month->format('w');
                    $days_in_month = (int) $this->current_month->format('t');
                    $days_in_week = 1;
                    $day_counter = 0;

                    // Add blank days until the first day of the month
                    for ($x = 0; $x < $running_day; $x++) {
                        echo '<td class="church_social_calendar__day church_social_calendar__day--blank"></td>';
                        $days_in_week++;
                    }

                    // Add days of the month
                    for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
                        $day = date_create($this->current_month->format('F').' '.$list_day.', '.$this->current_month->format('Y'));

                        $day_classes = [];

                        if ($day->format('Y-m-d') === date_create('today')->format('Y-m-d')) {
                            echo '<td class="church_social_calendar__day church_social_calendar__day--today">';
                        } else {
                            echo '<td class="church_social_calendar__day">';
                        }

                        echo '<div class="church_social_calendar__day_wrap">';
                        echo '<span class="church_social_calendar__day_number">'.$day->format('D j').'</span>';

                        foreach ($this->events as $event) {
                            if (date_create($event['date'])->format('Y-m-d') === $day->format('Y-m-d')) {
                                echo '<div class="church_social_calendar__event">';
                                echo '<a class="church_social_calendar__event_link" href="?event_id='.$event['id'].'&event_date='.date_create($event['date'])->format('Y-m-d').'">'.$event['title'].'</a>';
                                echo '</div>';
                            }
                        }

                        echo '</div>';
                        echo '</td>';

                        if ($running_day === 6) {
                            echo '</tr>';

                            if (($day_counter + 1) !== $days_in_month) {
                                echo '<tr>';
                            }

                            $running_day = -1;
                            $days_in_week = 0;
                        }

                        $days_in_week++;
                        $running_day++;
                        $day_counter++;
                    }

                    // Add blank days at the end of the month
                    if ($days_in_week < 8 and $days_in_week > 1) {
                        for ($x = 1; $x <= (8 - $days_in_week); $x++) {
                            echo '<td class="church_social_calendar__day church_social_calendar__day--blank"></td>';
                        }
                    }
                ?>
            </tr>
        </tbody>
    </table>
</div>