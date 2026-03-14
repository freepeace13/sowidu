import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

const locale = window.shared('app.locale');
const messages = window.shared('translation.messages');

console.info('Translation messages', messages);

export default new VueI18n({
    locale,
    messages
});