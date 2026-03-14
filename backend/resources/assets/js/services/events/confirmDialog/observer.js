import Observer from '~/services/events/observer'
import { MODULES } from '~/services/events/constants'
import EventHandler from '~/services/events/eventHandler';

export default new Observer({
    modules: MODULES.CONFIRM_DIALOG,
    events: [
        new EventHandler('ask', function (message) {
            return new Promise((resolve, reject) => {
                this._terminal.modal.append({
                    modal: require('~/components/layouts/Confirmation').default,
                    size: 'md',
                    title: 'Confirmation Dialog',
                    attrs: {
                        message
                    },
                    listeners: {
                        rejected: (modalId) => {
                            reject();
                            this._terminal.modal.close(modalId);
                        },
                        confirmed: (modalId) => {
                            resolve();
                            this._terminal.modal.close(modalId);
                        }
                    }
                });
            });
        })
    ]
});
