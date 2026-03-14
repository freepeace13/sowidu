import dayjs from 'dayjs'
import duration from 'dayjs/plugin/duration'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import { isNil } from './useUtils'

/**
 * @returns dayjs
 */
export function useDate() {
    return dayjs()
}

export function useDateNowIso() {
    return useDateNow().toISOString()
}

export function useDateNow(format = 'YYYY-MM-DD') {
    return useDate().format(format)
}

export function useDateIsBefore(date, compareTo = null, unit = 'day') {
    return dayjs(date).isBefore(compareTo ?? dayjs(), unit)
}

export function useGetTimeDuration(date, format = 'HH:mm:ss') {
    dayjs.extend(duration)
    const tz = useGetUserTimezone()

    return dayjs.duration(useDate().diff(dayjs(date).tz(tz))).format(format)
}

export function useGetUserTimezone() {
    dayjs.extend(utc)
    dayjs.extend(timezone)

    return dayjs.tz.guess()
}

export function useGetDurationFromSeconds(seconds) {
    let hours = Math.floor(seconds / (60 * 60))
    seconds = seconds - hours * 60 * 60

    let minutes = Math.floor(seconds / 60)
    seconds = seconds - minutes * 60

    return `${hours}h ${minutes}m ${seconds}s`
}

export function useDateFormat(date, format = 'DD.MM.YYYY', fallback = '--') {
    if (!dayjs(date).isValid() || isNil(date)) return fallback

    return dayjs(date).format(format)
}

export function useDateTimeLocal(date, format = 'ddd, MMM D, YYYY h:mm A') {
    if (!date) return 'Not set'

    if (!dayjs(date).isValid()) return ''

    return dayjs.utc(date).local().format(format)
}

export function useConvertDateTime(dateTime, fromFormat, toFormat) {
    dayjs.extend(customParseFormat)

    return dayjs(dateTime, fromFormat).format(toFormat)
}

export function useDateDiffInSeconds(dateFrom, dateTo, format = 'HH:mm') {
    dayjs.extend(customParseFormat)

    return dayjs(dateTo, format).diff(dayjs(dateFrom, format), 'second')
}
