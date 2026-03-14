/**
 * @params Object User
 * @params string permission name
 *
 * @return boolean
 */
export function grant(user, permission) {
    return user.can[permission]
}

/**
 * @params Object User
 * @params string permission name
 *
 * @return boolean
 */
export function deny(user, permission) {
    return !grant(user, permission)
}
