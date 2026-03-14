import { computed } from 'vue'
import { usePage } from '@inertiajs/vue2'

export function useAppService() {
    const { props } = usePage()

    const services = computed(() => props.services)

    // Method to check if a service will be shown based on user permissions
    function willBeShown(permission, name) {
        const { user } = props
        // Uncomment this block if you need to use impersonation logic
        // if (!user.impersonating) {
        //     // User is not impersonating, show all except "employees"
        //     return name != 'employees';
        // }

        // if (user.tenant.is_owner) {
        //     return true;
        // }

        if (name === 'account_settings') {
            return (
                !user.impersonating ||
                (user.impersonating && user.can['update settings'])
            )
        }

        return user.can[permission]
    }

    return {
        services,
        willBeShown,
    }
}
