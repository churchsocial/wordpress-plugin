<div class="church_social_sermon_archive">
    <div class="church_social_sermon_archive__sermon">
        <h2 class="church_social_sermon_archive__sermon_title"><?php echo $this->sermon['title'] ?></h2>

        <?php if ($this->sermon['description']): ?>
            <h3 class="church_social_sermon_archive__sermon_description_title">Description:</h3>
            <p class="church_social_sermon_archive__sermon_description">
                <?php echo nl2br($this->sermon['description']) ?>
            </p>
        <?php endif ?>

        <h3 class="church_social_sermon_archive__sermon_preached_title">Preached:</h3>
        <p class="church_social_sermon_archive__sermon_preached">
            <?php echo date_create($this->sermon['preached_date'], timezone_open(get_option('timezone_string')))->format('l, F j, Y') ?>
            <?php if ($this->sermon['preached_time']): ?>
                (<?php echo $this->sermon['preached_time'] ?>)
            <?php endif ?>
        </p>

        <?php if ($this->sermon['author']): ?>
            <h3 class="church_social_sermon_archive__sermon_author_title">Author:</h3>
            <p class="church_social_sermon_archive__sermon_author"><?php echo $this->sermon['author'] ?></p>
        <?php endif ?>

        <?php if ($this->sermon['texts']): ?>
            <h3 class="church_social_sermon_archive__sermon_texts_title">Texts:</h3>
            <ul class="church_social_sermon_archive__sermon_texts">
                <?php foreach ($this->sermon['texts'] as $text): ?>
                    <li class="church_social_sermon_archive__sermon_text"><?php echo $text ?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <?php foreach ($this->sermon['formats'] as $format): ?>
            <h3 class="church_social_sermon_archive__sermon_format_title"><?php echo $format['description'] ?>:</h3>

            <?php if ($format['type'] === 'Video'): ?>
                <p class="church_social_sermon_archive__sermon_format_type"><?php echo $format['video_code'] ?></p>
            <?php endif ?>

            <?php if ($format['type'] === 'Audio'): ?>
                <p>
                    <audio class="church_social_sermon_archive__sermon_audio_controls" controls>
                        <source src="<?php echo $format['audio_url'] ?>" type="audio/mpeg">
                    </audio>
                </p>
                <p>
                    <a class="church_social_sermon_archive__sermon_download_button" href="<?php echo $format['audio_url'] ?>">Download MP3</a>
                </p>
            <?php endif ?>
        <?php endforeach ?>

        <p>
            <a class="church_social_sermon_archive__sermon_back_button" href="<?php the_permalink() ?>">Back to sermons</a>
        </p>
    </div>
</div>
