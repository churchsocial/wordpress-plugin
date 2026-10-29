(function (blocks, blockEditor, components, element) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var Placeholder = components.Placeholder;
    var settings = window.churchSocialLivestream || {};

    blocks.registerBlockType('church-social/livestream', {
        edit: function (props) {
            var blockProps = useBlockProps();

            if (!settings.playerUrl) {
                return el('div', blockProps, el(Placeholder, {
                    icon: 'video-alt3',
                    label: 'Church Social Livestream',
                    instructions: 'Add your Church Social API key to show your livestream.',
                }, el('a', { href: settings.settingsUrl, target: '_blank', rel: 'noopener' }, 'Open Church Social settings')));
            }

            return el('div', blockProps, el('iframe', {
                src: settings.playerUrl,
                title: 'Livestream',
                allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share',
                referrerPolicy: 'strict-origin-when-cross-origin',
                allowFullScreen: true,
                style: {
                    display: 'block',
                    aspectRatio: '16 / 9',
                    width: '100%',
                    height: 'auto',
                    border: 0,
                    // Let clicks select the block until it's selected.
                    pointerEvents: props.isSelected ? 'auto' : 'none',
                },
            }));
        },
        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element);
