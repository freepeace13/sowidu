import AccountOverviewDrawer from '../components/AccountOverviewDrawer';
import config from '~/config';
import { KEYS, EVENTS } from '../enums';

const storage = config(`vuex.persistence.storage.secure-ls`);

const storagekey = KEYS.STORAGE.ACCOUNT_OVERVIEW_DRAWER;
const events = EVENTS.ACCOUNT_OVERVIEW_DRAWER;

if (!storage.getItem(storagekey)) {
    storage.setItem(storagekey, {
        open: true,
        mini: true
    });
}

export default {
    data: () => ({
        open: storage.getItem(storagekey).open,
        mini: storage.getItem(storagekey).mini
    }),

    methods: {
        toggle() {
            this.open = !this.open;
        }
    },

    created() {
        this.$events.$on(events.OPEN, (value) => this.open = !!value);
        this.$events.$on(events.MINI, (value) => this.mini = !!mini);
    },

    watch: {
        open(value) {
            storage.setItem(storagekey, { ...storage.getItem(storagekey),
                open: (value === undefined) ? !this.open : value
            });
        },

        mini(value) {
            storage.setItem(storagekey, { ...storage.getItem(storagekey),
                mini: (value === undefined) ? !this.mini : value
            });
        },
    },

    render(createElement) {
        const self = this;

        return createElement(AccountOverviewDrawer, {
            props: {
                account: self.$store.getters['auth/profile'](),
                mini: self.mini,
                open: self.open
            },
            on: {
                'update:mini': value => self.mini = value,
            }
        });
    }
}