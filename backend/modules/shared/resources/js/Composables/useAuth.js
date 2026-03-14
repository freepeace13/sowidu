import { usePage } from '@inertiajs/vue2'

export const canAny = (permissions) => {
    return permissions.some((p) => authCan(p))
}

export const canAll = (permissions) => {
    return permissions.every((p) => authCan(p))
}

export function authCan(permission) {
    const props = usePage().props
    const user = props?.user

    if (!user || user?.isGuest) {
        return false
    }

    if (Object.prototype.hasOwnProperty.call(user?.can, permission)) {
        return user.can[permission]
    }

    // Check `permissions` props for the permission
    const permissions = props?.permissions
    if (Object.prototype.hasOwnProperty.call(permissions, permission)) {
        return permissions[permission]
    }

    return false
}

export function authCannot(permission) {
    return !authCan(permission)
}

export function isAuthenticated() {
    const props = usePage().props
    const user = props?.user

    if (!user || user?.isGuest) {
        return false
    }

    return !user.isGuest
}
