import { getCurrentInstance, inject, provide } from 'vue'

/**
 * Creates a React Context-like helper for Vue 2 Composition API
 *
 * @param {*} defaultValue - Default value to use when context is consumed outside a provider
 * @returns {Object} Context object with Provider and useContext methods
 *
 * @example
 * const ThemeContext = createContext({ theme: 'light' })
 *
 * // In parent component:
 * ThemeContext.Provider({ theme: 'dark' })
 *
 * // In child component:
 * const { theme } = ThemeContext.useContext()
 */
export default function createContext(defaultValue = undefined) {
    // Generate a unique Symbol key for this context
    const contextKey = Symbol('Context')

    /**
     * Provider function that provides context value to child components
     * Should be called in the setup() function of a parent component
     *
     * @param {*} value - The context value to provide (can be reactive ref/reactive)
     */
    function Provider(value) {
        const instance = getCurrentInstance()

        if (!instance) {
            throw new Error(
                'createContext.Provider must be called within a component setup() function',
            )
        }

        // Provide the value using Vue's provide API
        provide(contextKey, value)
    }

    /**
     * Hook to consume context value from the nearest provider
     * Should be called in the setup() function of a child component
     *
     * @returns {*} The context value from the nearest provider, or defaultValue if no provider found
     */
    function useContext() {
        const instance = getCurrentInstance()

        if (!instance) {
            throw new Error(
                'createContext.useContext must be called within a component setup() function',
            )
        }

        // Inject the value, falling back to defaultValue if not found
        const value = inject(contextKey, defaultValue)

        return value
    }

    return {
        Provider,
        useContext,
        _contextKey: contextKey, // Expose for testing/debugging
    }
}
