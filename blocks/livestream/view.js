(function () {
    var REFRESH_INTERVAL = 60000;
    var BADGES = {
        live: 'Live',
        upcoming: 'Upcoming',
    };

    function isVideo(video) {
        return !!video && video.id != null && typeof video.scheduled_at === 'string' && typeof video.player_url === 'string' && /^https?:\/\//i.test(video.player_url);
    }

    // The church's clock, like "2026-11-01 09:15:00", read as it is so it shows the church's day and time.
    function scheduledAt(video) {
        var parts = video.scheduled_at.split(/\D/);

        return new Date(parts[0], parts[1] - 1, parts[2], parts[3] || 0, parts[4] || 0);
    }

    function dateLabel(video) {
        var date = scheduledAt(video);

        return date.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: date.getFullYear() === new Date().getFullYear() ? undefined : 'numeric',
        });
    }

    function partOfDay(video) {
        var hour = scheduledAt(video).getHours();

        return hour < 12 ? 'Morning' : hour < 17 ? 'Afternoon' : 'Evening';
    }

    function parse(json) {
        try {
            return JSON.parse(json);
        } catch (error) {
            return null;
        }
    }

    function span(className, text) {
        var element = document.createElement('span');
        element.className = className;
        element.textContent = text;

        return element;
    }

    function row(id, selected, children) {
        var item = document.createElement('li');
        item.className = 'church_social_livestream__video';

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'church_social_livestream__video_button';
        button.setAttribute('data-video-id', id);

        if (selected) {
            button.setAttribute('aria-current', 'true');
        }

        children.forEach(function (child, index) {
            button.append(index > 0 ? ' ' : '', child);
        });

        item.appendChild(button);

        return item;
    }

    function init(container) {
        var apiUrl = container.getAttribute('data-api-url');
        var data = parse(container.getAttribute('data-livestream'));
        var iframe = container.querySelector('iframe');
        var offline = container.querySelector('.church_social_livestream__offline');
        var list = container.querySelector('.church_social_livestream__videos');
        var pickedId = null;
        // A stream that was playing when the top moved on, kept until a new one goes live.
        var heldId = null;
        var lastRefreshedAt = 0;
        var loading = false;

        if (!data || !Array.isArray(data.videos) || !iframe || !offline || !list) {
            return;
        }

        function current() {
            return data.videos.find(function (video) {
                return video.current;
            }) || null;
        }

        function selected() {
            return data.videos.find(function (video) {
                return String(video.id) === (pickedId !== null ? pickedId : heldId);
            }) || current();
        }

        function videoRow(video, selectedId) {
            var children = [
                span('church_social_livestream__video_date', dateLabel(video)),
                span('church_social_livestream__video_part_of_day', '/ ' + partOfDay(video)),
            ];

            if (video.current && BADGES[video.status]) {
                children.push(span('church_social_livestream__badge church_social_livestream__badge_' + video.status, BADGES[video.status]));
            }

            if (video.title) {
                children.push(span('church_social_livestream__video_title', video.title));
            }

            return row(String(video.id), String(video.id) === selectedId, children);
        }

        function render() {
            var video = selected();
            var url = video ? video.player_url : 'about:blank';

            if ((iframe.getAttribute('src') || 'about:blank') !== url) {
                iframe.setAttribute('src', url);
            }

            offline.hidden = !!video;

            var selectedId = video ? String(video.id) : '';
            var focusedId = list.contains(document.activeElement) ? document.activeElement.getAttribute('data-video-id') : null;
            var rows = data.videos.map(function (video) {
                return videoRow(video, selectedId);
            });

            if (!current()) {
                rows.unshift(row('', selectedId === '', [
                    span('church_social_livestream__video_date', 'Livestream'),
                    span('church_social_livestream__badge church_social_livestream__badge_offline', 'Offline'),
                ]));
            }

            list.replaceChildren.apply(list, rows);

            if (focusedId !== null) {
                var button = list.querySelector('[data-video-id="' + CSS.escape(focusedId) + '"]');

                if (button) {
                    button.focus();
                }
            }
        }

        function update(next) {
            var previous = current();

            data = {
                videos: next.videos.filter(isVideo),
            };

            if (current() && current().status === 'live') {
                heldId = null;
            } else if (pickedId === null && heldId === null && previous && previous.status !== 'upcoming' && (!current() || current().id !== previous.id)) {
                heldId = String(previous.id);
            }

            render();
        }

        // Only polls from a player that has been open a while say they're watching, so crawlers don't count.
        function refresh(watching) {
            if (loading) {
                return;
            }

            loading = true;
            lastRefreshedAt = Date.now();

            fetch(watching ? apiUrl + (apiUrl.indexOf('?') === -1 ? '?' : '&') + 'watching=1' : apiUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Unable to load the livestream.');
                    }

                    return response.json();
                })
                .then(function (json) {
                    if (json && json.data && Array.isArray(json.data.videos)) {
                        update(json.data);
                    }
                })
                .catch(function () {})
                .then(function () {
                    loading = false;
                });
        }

        container.addEventListener('click', function (event) {
            var button = event.target.closest('.church_social_livestream__video_button');

            if (!button || !container.contains(button)) {
                return;
            }

            var id = button.getAttribute('data-video-id') || null;

            pickedId = current() && id === String(current().id) ? null : id;
            heldId = null;
            render();
        });

        render();

        // Pages can be served stale by caching plugins.
        refresh();

        setInterval(function () {
            if (document.visibilityState === 'visible') {
                refresh(true);
            }
        }, REFRESH_INTERVAL);

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible' && Date.now() - lastRefreshedAt >= REFRESH_INTERVAL) {
                refresh(true);
            }
        });
    }

    function initAll() {
        document.querySelectorAll('.church_social_livestream[data-livestream]').forEach(function (container) {
            if (container.hasAttribute('data-initialized')) {
                return;
            }

            container.setAttribute('data-initialized', '');
            init(container);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
