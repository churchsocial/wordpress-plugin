(function (blocks, blockEditor, components, element, ServerSideRender) {
    var el = element.createElement;
    var useBlockProps = blockEditor.useBlockProps;
    var Placeholder = components.Placeholder;
    var Disabled = components.Disabled;
    var settings = window.churchSocialCalendar || {};

    blocks.registerBlockType('church-social/calendar', {
        edit: function () {
            var blockProps = useBlockProps();

            if (!settings.hasApiKey) {
                return el('div', blockProps, el(Placeholder, {
                    icon: 'calendar-alt',
                    label: 'Church Social Calendar',
                    instructions: 'Add your Church Social API key to show your calendar.',
                }, el('a', { href: settings.settingsUrl, target: '_blank', rel: 'noopener' }, 'Open Church Social settings')));
            }

            // Show the real calendar, but keep its links from
            // working inside the editor.
            return el('div', blockProps, el(Disabled, null, el(ServerSideRender, {
                block: 'church-social/calendar',
            })));
        },
        save: function () {
            return null;
        },
    });
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.serverSideRender);
