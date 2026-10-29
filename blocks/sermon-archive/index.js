(function (blocks, blockEditor, components, element, ServerSideRender) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var Placeholder = components.Placeholder;
    var Disabled = components.Disabled;
    var settings = window.churchSocialSermonArchive || {};

    blocks.registerBlockType('church-social/sermon-archive', {
        edit: function () {
            var blockProps = useBlockProps();

            if (!settings.hasApiKey) {
                return el('div', blockProps, el(Placeholder, {
                    icon: 'microphone',
                    label: 'Church Social Sermon Archive',
                    instructions: 'Add your Church Social API key to show your sermons.',
                }, el('a', { href: settings.settingsUrl, target: '_blank', rel: 'noopener' }, 'Open Church Social settings')));
            }

            // Show the real sermon list, but keep its links and form from
            // working inside the editor.
            return el('div', blockProps, el(Disabled, null, el(ServerSideRender, {
                block: 'church-social/sermon-archive',
            })));
        },
        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.serverSideRender);
