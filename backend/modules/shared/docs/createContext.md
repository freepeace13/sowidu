# createContext - React Context Pattern for Vue 2

A helper function that brings React Context/Provider pattern to Vue 2 Composition API, enabling clean state sharing across component trees without prop drilling.

## Table of Contents

- [Introduction](#introduction)
- [Getting Started](#getting-started)
- [API Reference](#api-reference)
- [Usage Examples](#usage-examples)
- [Advanced Patterns](#advanced-patterns)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

## Introduction

### What is React Context Pattern?

The Context pattern allows you to share data across multiple levels of components without passing props through every intermediate component. This is especially useful for:

- Theme configuration
- User authentication state
- Form state management
- Global application settings
- Language/localization preferences

### Why Use Context in Vue?

While Vue provides `provide`/`inject` API, using it directly can be verbose and error-prone. The `createContext` helper provides:

- **Clean API**: Simple `Provider` and `useContext` methods
- **Type Safety**: Clear contracts for context values
- **Default Values**: Graceful fallbacks when providers are missing
- **Reactive Support**: Automatic reactivity preservation
- **Developer Experience**: Familiar pattern for React developers

### When to Use Context vs Props

**Use Context when:**
- Data needs to be accessed by many components at different nesting levels
- Data is relatively static or changes infrequently
- You want to avoid prop drilling through many intermediate components
- Data represents "global" concerns (theme, auth, locale)

**Use Props when:**
- Data is only needed by direct children
- Data changes frequently and needs explicit parent-child communication
- You want explicit data flow for better component reusability

## Getting Started

### Installation

The `createContext` helper is available in the shared module:

```javascript
import createContext from '~Shared/Composables/createContext'
```

### Basic Example

```javascript
// 1. Create a context with a default value
const ThemeContext = createContext({ theme: 'light' })

// 2. Provide context value in parent component
export default {
  setup() {
    const theme = ref('dark')
    ThemeContext.Provider({ theme: theme.value })
    return {}
  }
}

// 3. Consume context in child component
export default {
  setup() {
    const context = ThemeContext.useContext()
    return {
      theme: context.theme
    }
  }
}
```

## API Reference

### `createContext(defaultValue)`

Creates a new context object with Provider and useContext methods.

**Parameters:**
- `defaultValue` (optional): The default value to use when context is consumed outside a provider. Can be any type (object, primitive, reactive ref, etc.).

**Returns:**
An object with the following properties:
- `Provider(value)`: Function to provide context value
- `useContext()`: Function to consume context value
- `_contextKey`: Symbol key (for testing/debugging)

**Example:**
```javascript
const MyContext = createContext({ name: 'Default' })
```

### `Context.Provider(value)`

Provides a context value to all child components.

**Parameters:**
- `value`: The context value to provide. Can be:
  - Plain object: `{ theme: 'dark' }`
  - Reactive ref: `ref({ theme: 'dark' })`
  - Reactive object: `reactive({ theme: 'dark' })`
  - Primitive: `'dark'`

**Usage:**
Must be called within a component's `setup()` function.

```javascript
export default {
  setup() {
    const theme = ref('dark')
    MyContext.Provider({ theme: theme.value })
    return {}
  }
}
```

**Note:** The value is automatically made reactive if you pass a `ref` or `reactive` object. Changes to reactive values will automatically update consuming components.

### `Context.useContext()`

Consumes the context value from the nearest provider in the component tree.

**Returns:**
The context value from the nearest provider, or the `defaultValue` if no provider is found.

**Usage:**
Must be called within a component's `setup()` function.

```javascript
export default {
  setup() {
    const context = MyContext.useContext()
    return {
      theme: context.theme
    }
  }
}
```

**Reactivity:**
If the provided value is reactive (ref or reactive), the returned value will also be reactive and will update automatically when the provider's value changes.

## Usage Examples

### Theme Context

A common use case for managing application theme:

```javascript
// contexts/ThemeContext.js
import createContext from '~Shared/Composables/createContext'

export const ThemeContext = createContext({
  theme: 'light',
  toggleTheme: () => {}
})
```

```vue
<!-- App.vue -->
<script>
import { ref } from 'vue'
import { ThemeContext } from './contexts/ThemeContext'

export default {
  setup() {
    const theme = ref('light')
    
    const toggleTheme = () => {
      theme.value = theme.value === 'light' ? 'dark' : 'light'
    }
    
    ThemeContext.Provider({
      theme: theme.value,
      toggleTheme
    })
    
    return {}
  }
}
</script>
```

```vue
<!-- ThemedButton.vue -->
<script>
import { ThemeContext } from './contexts/ThemeContext'

export default {
  setup() {
    const { theme, toggleTheme } = ThemeContext.useContext()
    
    return {
      theme,
      toggleTheme
    }
  }
}
</script>

<template>
  <button 
    :class="`theme-${theme}`"
    @click="toggleTheme"
  >
    Toggle Theme
  </button>
</template>
```

### User/Auth Context

Managing authenticated user state:

```javascript
// contexts/AuthContext.js
import createContext from '~Shared/Composables/createContext'
import { usePage } from '@inertiajs/vue2'

export const AuthContext = createContext({
  user: null,
  isAuthenticated: false,
  isGuest: true
})
```

```vue
<!-- Layout.vue -->
<script>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue2'
import { AuthContext } from './contexts/AuthContext'

export default {
  setup() {
    const page = usePage()
    const user = computed(() => page.props.value?.user)
    
    AuthContext.Provider({
      user: user.value,
      isAuthenticated: !!user.value && !user.value.isGuest,
      isGuest: !user.value || user.value.isGuest
    })
    
    return {}
  }
}
</script>
```

```vue
<!-- UserMenu.vue -->
<script>
import { AuthContext } from './contexts/AuthContext'

export default {
  setup() {
    const { user, isAuthenticated } = AuthContext.useContext()
    
    return {
      user,
      isAuthenticated
    }
  }
}
</script>

<template>
  <div v-if="isAuthenticated">
    <span>Welcome, {{ user.name }}</span>
  </div>
</template>
```

### Form Context

Sharing form state across multiple form components:

```javascript
// contexts/FormContext.js
import createContext from '~Shared/Composables/createContext'
import { useForm } from '@inertiajs/vue2'

export const FormContext = createContext({
  form: null,
  errors: {},
  isSubmitting: false
})
```

```vue
<!-- FormContainer.vue -->
<script>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue2'
import { FormContext } from './contexts/FormContext'

export default {
  setup() {
    const form = useForm({
      name: '',
      email: '',
      message: ''
    })
    
    const isSubmitting = ref(false)
    
    FormContext.Provider({
      form,
      errors: form.errors,
      isSubmitting: isSubmitting.value
    })
    
    const submit = () => {
      isSubmitting.value = true
      form.post('/contact', {
        onFinish: () => {
          isSubmitting.value = false
        }
      })
    }
    
    return { submit }
  }
}
</script>
```

```vue
<!-- FormInput.vue -->
<script>
import { FormContext } from './contexts/FormContext'

export default {
  props: {
    name: String,
    label: String
  },
  setup(props) {
    const { form, errors } = FormContext.useContext()
    
    return {
      form,
      errors
    }
  }
}
</script>

<template>
  <div>
    <label>{{ label }}</label>
    <input 
      v-model="form[props.name]"
      :name="props.name"
    />
    <span v-if="errors[props.name]" class="error">
      {{ errors[props.name] }}
    </span>
  </div>
</template>
```

### Reactive State Updates

Context values can be reactive, automatically updating consuming components:

```javascript
// contexts/CounterContext.js
import createContext from '~Shared/Composables/createContext'
import { ref } from 'vue'

export const CounterContext = createContext({
  count: ref(0),
  increment: () => {},
  decrement: () => {}
})
```

```vue
<!-- CounterProvider.vue -->
<script>
import { ref } from 'vue'
import { CounterContext } from './contexts/CounterContext'

export default {
  setup() {
    const count = ref(0)
    
    const increment = () => {
      count.value++
    }
    
    const decrement = () => {
      count.value--
    }
    
    // Pass reactive ref - changes will propagate automatically
    CounterContext.Provider({
      count,
      increment,
      decrement
    })
    
    return {}
  }
}
</script>
```

```vue
<!-- CounterDisplay.vue -->
<script>
import { CounterContext } from './contexts/CounterContext'

export default {
  setup() {
    const { count } = CounterContext.useContext()
    
    // count is reactive - will update when provider changes it
    return {
      count
    }
  }
}
</script>

<template>
  <div>Count: {{ count }}</div>
</template>
```

## Advanced Patterns

### Nested Contexts

You can nest multiple contexts - child providers override parent providers:

```javascript
const ThemeContext = createContext({ theme: 'light' })
const LanguageContext = createContext({ lang: 'en' })
```

```vue
<!-- App.vue -->
<script>
export default {
  setup() {
    ThemeContext.Provider({ theme: 'dark' })
    LanguageContext.Provider({ lang: 'en' })
    return {}
  }
}
</script>

<template>
  <div>
    <NestedComponent />
  </div>
</template>
```

```vue
<!-- NestedComponent.vue -->
<script>
export default {
  setup() {
    // Can override parent context
    ThemeContext.Provider({ theme: 'light' })
    
    // Or use parent context
    const { lang } = LanguageContext.useContext()
    
    return { lang }
  }
}
</script>
```

### Combining Multiple Contexts

Use multiple contexts in a single component:

```vue
<script>
import { ThemeContext } from './contexts/ThemeContext'
import { AuthContext } from './contexts/AuthContext'
import { LanguageContext } from './contexts/LanguageContext'

export default {
  setup() {
    const theme = ThemeContext.useContext()
    const auth = AuthContext.useContext()
    const lang = LanguageContext.useContext()
    
    return {
      theme,
      auth,
      lang
    }
  }
}
</script>
```

### Conditional Providers

Provide context conditionally:

```vue
<script>
import { AuthContext } from './contexts/AuthContext'
import { usePage } from '@inertiajs/vue2'

export default {
  setup() {
    const page = usePage()
    const user = page.props.value?.user
    
    if (user) {
      AuthContext.Provider({
        user,
        isAuthenticated: true
      })
    }
    
    return {}
  }
}
</script>
```

### Dynamic Context Values

Update context values dynamically:

```vue
<script>
import { ref, watch } from 'vue'
import { ThemeContext } from './contexts/ThemeContext'

export default {
  setup() {
    const theme = ref('light')
    
    // Update context when theme changes
    watch(theme, (newTheme) => {
      ThemeContext.Provider({ theme: newTheme })
    }, { immediate: true })
    
    const toggleTheme = () => {
      theme.value = theme.value === 'light' ? 'dark' : 'light'
    }
    
    return { toggleTheme }
  }
}
</script>
```

## Best Practices

### When to Use Context

✅ **Good use cases:**
- Theme/UI preferences
- Authentication state
- Language/localization
- Feature flags
- Global configuration
- Form state (when form spans multiple components)

❌ **Avoid using context for:**
- Frequently changing data (use props or events)
- Data only needed by direct children
- Component-specific state (use local state)
- Tightly coupled parent-child communication

### Performance Optimization

1. **Use reactive refs for frequently changing data:**
   ```javascript
   // Good - reactive updates are efficient
   const count = ref(0)
   CounterContext.Provider({ count })
   
   // Less efficient - creates new object on each change
   CounterContext.Provider({ count: count.value })
   ```

2. **Avoid providing large objects:**
   ```javascript
   // Good - provide only what's needed
   UserContext.Provider({ 
     id: user.id, 
     name: user.name 
   })
   
   // Avoid - provides entire user object
   UserContext.Provider({ user })
   ```

3. **Memoize context values when appropriate:**
   ```javascript
   import { computed } from 'vue'
   
   const expensiveValue = computed(() => {
     // Expensive computation
     return heavyCalculation()
   })
   
   MyContext.Provider({ value: expensiveValue.value })
   ```

### Avoiding Common Mistakes

1. **Don't call Provider/useContext outside setup():**
   ```javascript
   // ❌ Wrong
   export default {
     created() {
       ThemeContext.Provider({ theme: 'dark' }) // Error!
     }
   }
   
   // ✅ Correct
   export default {
     setup() {
       ThemeContext.Provider({ theme: 'dark' })
       return {}
     }
   }
   ```

2. **Don't forget to return context values:**
   ```javascript
   // ❌ Wrong - context not accessible in template
   export default {
     setup() {
       const { theme } = ThemeContext.useContext()
       // Forgot to return!
     }
   }
   
   // ✅ Correct
   export default {
     setup() {
       const { theme } = ThemeContext.useContext()
       return { theme }
     }
   }
   ```

3. **Use default values for optional contexts:**
   ```javascript
   // ✅ Good - provides safe fallback
   const OptionalContext = createContext({ 
     enabled: false 
   })
   
   // Component can safely use context
   const { enabled } = OptionalContext.useContext()
   ```

### Testing Strategies

1. **Mock context in tests:**
   ```javascript
   import { ThemeContext } from './contexts/ThemeContext'
   
   test('component uses theme', () => {
     const wrapper = mount(MyComponent, {
       setup() {
         ThemeContext.Provider({ theme: 'dark' })
       }
     })
     
     expect(wrapper.find('.theme-dark').exists()).toBe(true)
   })
   ```

2. **Test default values:**
   ```javascript
   test('component uses default theme', () => {
     const wrapper = mount(MyComponent)
     // No provider - should use default
     expect(wrapper.find('.theme-light').exists()).toBe(true)
   })
   ```

## Troubleshooting

### Common Errors

#### Error: "createContext.Provider must be called within a component setup() function"

**Cause:** You're calling `Provider` outside of a component's `setup()` function.

**Solution:**
```javascript
// ❌ Wrong
export default {
  created() {
    ThemeContext.Provider({ theme: 'dark' })
  }
}

// ✅ Correct
export default {
  setup() {
    ThemeContext.Provider({ theme: 'dark' })
    return {}
  }
}
```

#### Error: "createContext.useContext must be called within a component setup() function"

**Cause:** You're calling `useContext` outside of a component's `setup()` function.

**Solution:**
```javascript
// ❌ Wrong
export default {
  mounted() {
    const { theme } = ThemeContext.useContext()
  }
}

// ✅ Correct
export default {
  setup() {
    const { theme } = ThemeContext.useContext()
    return { theme }
  }
}
```

#### Context value is undefined

**Cause:** No provider found in component tree, and no default value was provided.

**Solution:**
```javascript
// Provide a default value
const MyContext = createContext({ value: 'default' })

// Or ensure provider exists
export default {
  setup() {
    MyContext.Provider({ value: 'actual' })
    return {}
  }
}
```

#### Reactive updates not working

**Cause:** You're providing a plain value instead of a reactive ref.

**Solution:**
```javascript
// ❌ Wrong - plain value, not reactive
const count = 0
CounterContext.Provider({ count })

// ✅ Correct - reactive ref
const count = ref(0)
CounterContext.Provider({ count })
```

### Debugging Tips

1. **Check if provider exists:**
   ```javascript
   const context = ThemeContext.useContext()
   console.log('Context value:', context)
   ```

2. **Verify context key:**
   ```javascript
   const ThemeContext = createContext({ theme: 'light' })
   console.log('Context key:', ThemeContext._contextKey)
   ```

3. **Use Vue DevTools:**
   - Inspect component tree to see provide/inject relationships
   - Check reactive values in the component state

### Migration from Props Drilling

If you're migrating from prop drilling to context:

**Before (Props Drilling):**
```vue
<!-- App.vue -->
<template>
  <Parent :theme="theme" />
</template>

<!-- Parent.vue -->
<template>
  <Child :theme="theme" />
</template>

<!-- Child.vue -->
<template>
  <GrandChild :theme="theme" />
</template>
```

**After (Context):**
```vue
<!-- App.vue -->
<script>
export default {
  setup() {
    const theme = ref('dark')
    ThemeContext.Provider({ theme: theme.value })
    return {}
  }
}
</script>

<!-- Parent.vue -->
<template>
  <Child />
</template>

<!-- Child.vue -->
<template>
  <GrandChild />
</template>

<!-- GrandChild.vue -->
<script>
export default {
  setup() {
    const { theme } = ThemeContext.useContext()
    return { theme }
  }
}
</script>
```

### Performance Considerations

1. **Context updates trigger re-renders:** All consuming components will re-render when context value changes. Use this sparingly for frequently changing data.

2. **Nested providers:** Child providers override parent providers. This is by design but can be confusing if not documented.

3. **Memory:** Context values are stored in Vue's internal provide/inject system. Large objects can impact memory usage.

## Additional Resources

- [Vue 2 Composition API Documentation](https://v2.vuejs.org/v2/guide/composition-api.html)
- [Vue Provide/Inject API](https://v2.vuejs.org/v2/api/#provide-inject)
- [React Context Documentation](https://react.dev/reference/react/createContext) (for comparison)

