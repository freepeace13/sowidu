import Vue from 'vue'

Vue.filter('truncate', function (string, length, separator = '...') {
    if (string.length <= length) return string
    return `${string.substring(0, length)}${separator}`
})
