<?php if ($livestream && isset($livestream['videos'])): ?>
    <?php $current = array_values(array_filter($livestream['videos'], function ($video) { return !empty($video['current']); })) ?>
    <div class="church_social_livestream" data-api-url="<?php echo esc_url($this->getApiUrl()) ?>" data-livestream="<?php echo esc_attr(wp_json_encode($livestream)) ?>">
        <div class="church_social_livestream__player">
            <?php echo $this->renderPlayer($current ? $current[0]['player_url'] : null) ?>
            <div class="church_social_livestream__offline"<?php if ($current): ?> hidden<?php endif ?>>Livestream is offline.</div>
        </div>
        <ul class="church_social_livestream__videos" aria-label="Streams"></ul>
    </div>
<?php else: ?>
    <div class="church_social_livestream">
        <?php echo $this->renderPlayer($livestream ? $livestream['player_url'] : $this->getPlayerUrl()) ?>
    </div>
<?php endif ?>
