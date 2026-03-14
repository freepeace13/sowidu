import Vue from 'vue'
import dayjs from 'dayjs'
import LocalizedFormat from 'dayjs/plugin/localizedFormat'
import UTC from 'dayjs/plugin/utc'

dayjs.extend(LocalizedFormat)

Vue.filter(
    'formatDate',
    function (date, format = 'YYYY-MM-DD', fallback = '--') {
        if (!dayjs(date).isValid()) return fallback

        return dayjs(date).format(format)
    },
)

dayjs.extend(UTC)

Vue.filter(
    'toDateTimeLocal',
    function (date, format = 'ddd, MMM D, YYYY h:mm A') {
        if (!date) return 'Not set'

        if (!dayjs(date).isValid()) return ''

        return dayjs.utc(date).local().format(format)
    },
)
