const result = process.versions
const nodeVersion = '22.8.0'

if (result && result.node) {
    if (String(result.node) != `${nodeVersion}`) {
        console.log(
            '\x1b[41m\x1b[37m%s\x1b[0m',
            `-------******* Please install and use Node Version = ${nodeVersion} *******-------`,
        )
        console.log(
            '\x1b[42m\x1b[37m%s\x1b[0m',
            '-------******* Your current Node Version is ' +
                result.node +
                '        *******-------',
        )
        console.log(
            '\x1b[34m\x1b[44m\x1b[31m%s\x1b[0m',
            '-------******* Run `nvm use` to change node version        *******-------',
        )

        process.exit(1)
    }
    console.log(
        '\x1b[42m\x1b[37m%s\x1b[0m',
        '-------******* Good to Go with your Node Version: ' +
            result.node +
            ' *******-------',
    )
} else {
    console.log(
        '\x1b[47m\x1b[31m%s\x1b[0m',
        '-------******* Something went wrong while checking Node version *******-------',
    )
    process.exit(1)
}
