import Vue from 'vue'

//** Plugins
import Confirm from './Confirm'
import InertiaHelper from './InertiaHelper'

Vue.use(Confirm)
Vue.mixin(InertiaHelper)
