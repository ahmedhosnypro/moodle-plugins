"use strict";
define(['jquery', 'block_remuiblck/Plugin'], function($, Plugin) {
    const NAME = 'pieProgress';

    class PieProgress extends Plugin {
        getName() {
            return NAME;
        }

        static getDefaults() {
            return {
                namespace: 'pie-progress',
                speed: 30,
                classes: {
                    svg: 'pie-progress-svg',
                    element: 'pie-progress',
                    number: 'pie-progress-number',
                    content: 'pie-progress-content'
                }
            };
        }

        render() {
            if (!$.fn.asPieProgress) {
                return;
            }

            let $el = this.$el;

            $el.asPieProgress(this.options);
        }
    }

    Plugin.register(NAME, PieProgress);

    return PieProgress;
});